package com.scieventlink.app.models;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Log;

import com.android.volley.AuthFailureError;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.scieventlink.app.listeners.FavoriteListener;
import com.scieventlink.app.listeners.LoginListener;
import com.scieventlink.app.utils.EventJsonParser;
import com.scieventlink.app.utils.FavoritesJsonParser;
import com.scieventlink.app.utils.LoginJsonParser;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public class SingletonManager {

    private static SingletonManager instance = null;
    private static RequestQueue volleyQueue = null;

    private static final String BASE_URL = "http://172.22.21.248/scieventlink/WebApp/backend/web/api";

    private static final String PREF_NAME = "SciEventLinkPrefs";
    private static final String KEY_TOKEN = "auth_token";
    private static final String KEY_USERNAME = "username";
    private static final String KEY_USER_ID = "user_id";


    private Context context;
    private LoginListener loginListener;

    // Timeout de 10 segundos para a VPN do IPLeiria
    private static final int TIMEOUT_MS = 10000;

    private SingletonManager(Context context) {
        this.context = context.getApplicationContext();
        volleyQueue = Volley.newRequestQueue(this.context);
    }

    public static synchronized SingletonManager getInstance(Context context) {
        if (instance == null) {
            instance = new SingletonManager(context);
        }
        return instance;
    }

    // --- GESTÃO DE TOKEN ---

    public String getAccessToken() {
        SharedPreferences prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return prefs.getString(KEY_TOKEN, null);
    }

    // --- MÉTODOS GENÉRICOS (BOAS PRÁTICAS) ---

    private Map<String, String> getAuthHeaders() {
        Map<String, String> headers = new HashMap<>();
        String token = getAccessToken();
        if (token != null) {
            headers.put("Authorization", "Bearer " + token);
        }
        return headers;
    }

    private void applyRetryPolicy(Request<?> request) {
        request.setRetryPolicy(new DefaultRetryPolicy(
                TIMEOUT_MS,
                DefaultRetryPolicy.DEFAULT_MAX_RETRIES,
                DefaultRetryPolicy.DEFAULT_BACKOFF_MULT));
    }

    // --- LOGIN ---

    public void setLoginListener(LoginListener listener) {
        this.loginListener = listener;
    }

    public void loginAPI(final String username, final String password, final Context ctx) {
        String url = BASE_URL + "/auth/login";
        JSONObject jsonBody = new JSONObject();
        try {
            jsonBody.put("username", username);
            jsonBody.put("password", password);
        } catch (JSONException e) { e.printStackTrace(); }

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, jsonBody,
                response -> {
                    String token = LoginJsonParser.parserJsonLogin(response.toString());

                    if (token != null) {
                        int userId = response.optInt("user_id", -1);
                        saveUserData(token, username, userId);

                        if (loginListener != null) loginListener.onValidateLogin(token, username, ctx);
                    } else {
                        if (loginListener != null) loginListener.onLoginError("Credenciais inválidas ou Token não encontrado.");
                    }
                },
                error -> {
                    if (loginListener != null) loginListener.onLoginError(handleVolleyError(error));
                }
        );
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }

    private void saveUserData(String token, String username, int userId) {
        SharedPreferences prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        prefs.edit()
                .putString(KEY_TOKEN, token)
                .putString(KEY_USERNAME, username)
                .putInt(KEY_USER_ID, userId)
                .apply();
    }

    // --- EVENTOS ---

    public void getAllEvents(final EventsListener listener) {
        String url = BASE_URL + "/events";
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.GET, url, null,
                response -> {
                    ArrayList<Event> events = EventJsonParser.parserJsonEvents(response.toString());
                    if (listener != null) listener.onEventsLoaded(events);
                },
                error -> {
                    if (listener != null) listener.onEventsError(handleVolleyError(error));
                }
        ) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }

    public void getEventDetails(int eventId, final EventDetailsListener listener) {
        String url = BASE_URL + "/events/" + eventId;

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.GET, url, null,
                response -> {
                    Event event = EventJsonParser.parserEventDetails(response.toString());

                    if (event != null) {
                        listener.onEventDetailsLoaded(event);
                    } else {
                        listener.onError("Erro ao processar dados do evento.");
                    }
                },
                error -> listener.onError(handleVolleyError(error))) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }

    // --- FAVORITOS (NOVO) ---

    public void getFavorites(final FavoriteListener listener) {
        String url = BASE_URL + "/favorites";
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.GET, url, null,
                response -> {
                    ArrayList<Session> favorites = FavoritesJsonParser.parserFavorites(response.toString());
                    listener.onFavoritesLoaded(favorites);
                },
                error -> listener.onError(handleVolleyError(error))
        ) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }
    public void toggleFavorite(Session session, final FavoriteListener listener) {
        if (session.isFavorite()) removeFavorite(session, listener);
        else addFavorite(session, listener);
    }

    private void addFavorite(Session session, final FavoriteListener listener) {
        String url = BASE_URL + "/favorites";
        JSONObject body = new JSONObject();
        try { body.put("session_id", session.getId()); } catch (JSONException e) { e.printStackTrace(); }

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, body,
                response -> {
                    int newId = FavoritesJsonParser.parserAddFavoriteResponse(response.toString());

                    session.setFavorite(true);
                    if (newId != -1) session.setFavoriteId(newId);

                    if (listener != null) listener.onFavoriteChanged(session.getId(), true);
                },
                error -> {
                    if (error.networkResponse != null && error.networkResponse.statusCode == 500) {
                        session.setFavorite(true);
                        if (listener != null) listener.onFavoriteChanged(session.getId(), true);
                    } else {
                        if (listener != null) listener.onError(handleVolleyError(error));
                    }
                }
        ) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }

    private void removeFavorite(Session session, final FavoriteListener listener) {
        String url = BASE_URL + "/favorites/" + session.getId();

        StringRequest request = new StringRequest(Request.Method.DELETE, url,
                response -> {
                    session.setFavorite(false);
                    if (listener != null) listener.onFavoriteChanged(session.getId(), false);
                },
                error -> {
                    if (listener != null) listener.onError(handleVolleyError(error));
                }) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }


    private String handleVolleyError(VolleyError error) {
        if (error.networkResponse != null) {
            int code = error.networkResponse.statusCode;
            if (code == 401) return "Sessão expirada. Faça login novamente.";
            if (code == 403) return "Sem permissão.";
            if (code == 500) return "Erro interno do servidor.";
            return "Erro no servidor (Código: " + code + ")";
        }
        return "Sem conexão à internet ou VPN inativa.";
    }

    public interface EventsListener {
        void onEventsLoaded(ArrayList<Event> events);
        void onEventsError(String message);
    }

    public interface EventDetailsListener {
        void onEventDetailsLoaded(Event event);
        void onError(String message);
    }
}