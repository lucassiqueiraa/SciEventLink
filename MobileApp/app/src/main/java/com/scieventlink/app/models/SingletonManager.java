package com.scieventlink.app.models;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Log;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonArrayRequest; // NOVO
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.scieventlink.app.listeners.LoginListener;
import com.scieventlink.app.utils.EventJsonParser; // NOVO
import com.scieventlink.app.utils.LoginJsonParser;

import org.json.JSONArray; // NOVO
import org.json.JSONObject;

import java.util.ArrayList; // NOVO
import java.util.HashMap;   // NOVO
import java.util.Map;       // NOVO

public class SingletonManager {

    private static SingletonManager instance = null;
    private static RequestQueue volleyQueue = null;

    // Configuração da API
    private static final String BASE_URL = "http://172.22.21.248/scieventlink/WebApp/backend/web/api";

    // Dados Locais (SharedPreferences)
    private static final String PREF_NAME = "SciEventLinkPrefs";
    private static final String KEY_TOKEN = "auth_token";
    private static final String KEY_USERNAME = "username";

    private LoginListener loginListener;
    private Context context;

    private SingletonManager(Context context) {
        this.context = context;
        volleyQueue = Volley.newRequestQueue(context);
    }

    public static synchronized SingletonManager getInstance(Context context) {
        if (instance == null) {
            instance = new SingletonManager(context);
        }
        return instance;
    }

    // --- MÉTODOS AUXILIARES ---

    public String getAccessToken() {
        SharedPreferences prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return prefs.getString(KEY_TOKEN, null);
    }

    public void setLoginListener(LoginListener listener) {
        this.loginListener = listener;
    }

    public void loginAPI(final String username, final String password, final Context context) {
        String url = BASE_URL + "/auth/login";

        JSONObject jsonBody = new JSONObject();
        try {
            jsonBody.put("username", username);
            jsonBody.put("password", password);
        } catch (Exception e) {
            e.printStackTrace();
        }

        Log.d("API_LOGIN", "Tentando login em: " + url);

        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.POST,
                url,
                jsonBody,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        Log.d("API_LOGIN", "Resposta: " + response.toString());
                        String token = LoginJsonParser.parserJsonLogin(response.toString());

                        if (token != null) {
                            SharedPreferences prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
                            SharedPreferences.Editor editor = prefs.edit();
                            editor.putString(KEY_TOKEN, token);
                            editor.putString(KEY_USERNAME, username);
                            editor.apply();

                            if (loginListener != null) {
                                loginListener.onValidateLogin(token, username, context);
                            }
                        } else {
                            if (loginListener != null) loginListener.onLoginError("Erro: Token não encontrado.");
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        String msg = "Erro de conexão";
                        if (error.networkResponse != null) {
                            if (error.networkResponse.statusCode == 401) msg = "Credenciais inválidas.";
                            else if (error.networkResponse.statusCode == 404) msg = "Servidor não encontrado.";
                        }
                        if (loginListener != null) loginListener.onLoginError(msg);
                    }
                }
        );
        volleyQueue.add(request);
    }


    public void getAllEvents(final EventsListener listener) {
        String url = BASE_URL + "/events";

        JsonArrayRequest request = new JsonArrayRequest(
                Request.Method.GET,
                url,
                null,
                new Response.Listener<JSONArray>() {
                    @Override
                    public void onResponse(JSONArray response) {
                        ArrayList<Event> events = EventJsonParser.parserJsonEvents(response.toString());

                        if (listener != null) {
                            listener.onEventsLoaded(events);
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        String errorMsg = "Erro ao carregar eventos.";
                        if (error.networkResponse != null) {
                            errorMsg += " (Código: " + error.networkResponse.statusCode + ")";
                        }
                        if (listener != null) {
                            listener.onEventsError(errorMsg);
                        }
                    }
                }
        ) {
            @Override
            public Map<String, String> getHeaders() {
                Map<String, String> headers = new HashMap<>();
                String token = getAccessToken();
                if (token != null) {
                    headers.put("Authorization", "Bearer " + token);
                }
                return headers;
            }
        };

        volleyQueue.add(request);
    }

    public interface EventsListener {
        void onEventsLoaded(ArrayList<Event> events);
        void onEventsError(String message);
    }

}