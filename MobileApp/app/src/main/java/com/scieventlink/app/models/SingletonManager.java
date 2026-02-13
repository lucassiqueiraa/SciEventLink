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
                    try {
                        Event event = parseEventWithSessions(response);
                        if (listener != null) listener.onEventDetailsLoaded(event);
                    } catch (Exception e) {
                        if (listener != null) listener.onError("Erro no parse: " + e.getMessage());
                    }
                },
                error -> {
                    if (listener != null) listener.onError(handleVolleyError(error));
                }
        ) {
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
                    ArrayList<Session> favorites = new ArrayList<>();
                    try {
                        for (int i = 0; i < response.length(); i++) {
                            JSONObject obj = response.getJSONObject(i);
                            // Como seu JSON não tem o ID do registro (vimos no print),
                            // usamos o session_id para identificar e para apagar.
                            int sId = obj.getInt("session_id");

                            Session s = new Session(sId, obj.optString("title"), "", "", "", 0);
                            s.setFavorite(true);
                            s.setFavoriteId(sId); // Se a sua API permitir DELETE /favorites/{session_id}
                            favorites.add(s);
                        }
                        listener.onFavoritesLoaded(favorites);
                    } catch (JSONException e) { listener.onError("Erro parse favoritos"); }
                }, error -> listener.onError(error.toString())
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
        try {
            body.put("session_id", session.getId());
        } catch (JSONException e) { e.printStackTrace(); }

        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, body,
                response -> {
                    try {
                        int newId = -1;
                        if (response.has("id")) newId = response.getInt("id");
                        else if (response.has("id_favorito")) newId = response.getInt("id_favorito");

                        session.setFavorite(true);
                        if (newId != -1) session.setFavoriteId(newId);

                        if (listener != null) listener.onFavoriteChanged(session.getId(), true);
                    } catch (JSONException e) {
                        session.setFavorite(true);
                        if (listener != null) listener.onFavoriteChanged(session.getId(), true);
                    }
                },
                error -> {
                    if (error.networkResponse != null && error.networkResponse.statusCode == 500) {
                        Log.w("Singleton", "Erro 500 ignorado: assumindo sucesso.");

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
        int idToDelete = session.getFavoriteId();
        if (idToDelete <= 0) {
            listener.onError("Erro: ID do favorito não sincronizado.");
            return;
        }
        String url = BASE_URL + "/favorites/" + idToDelete;
        StringRequest request = new StringRequest(Request.Method.DELETE, url,
                response -> {
                    session.setFavorite(false);
                    session.setFavoriteId(-1);
                    listener.onFavoriteChanged(session.getId(), false);
                }, error -> listener.onError(error.toString())) {
            @Override public Map<String, String> getHeaders() { return getAuthHeaders(); }
        };
        applyRetryPolicy(request);
        volleyQueue.add(request);
    }

    // --- AUXILIARES DE PARSE E ERRO ---

    private Event parseEventWithSessions(JSONObject response) throws JSONException {
        Event event = new Event(
                response.getInt("id"),
                response.getString("name"),
                response.optString("description", ""),
                response.getString("start_date"),
                response.getString("end_date"),
                response.getString("status")
        );
        ArrayList<Session> sessions = new ArrayList<>();
        if (response.has("sessions")) {
            JSONArray arr = response.getJSONArray("sessions");
            for (int i = 0; i < arr.length(); i++) {
                sessions.add(parseSession(arr.getJSONObject(i)));
            }
        }
        event.setSessions(sessions);
        return event;
    }

    private Session parseSession(JSONObject s) throws JSONException {
        Session session = new Session(
                s.getInt("id"),
                s.getString("title"),
                s.getString("start_time"),
                s.getString("end_time"),
                s.optString("location", "TBA"),
                s.optInt("capacity", 0)
        );
        // Se a API retornar is_favorite diretamente no detalhe do evento, fazemos o set aqui:
        session.setFavorite(s.optBoolean("is_favorite", false));
        return session;
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