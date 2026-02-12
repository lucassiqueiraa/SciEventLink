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

    public void getEventDetails(int eventId, final EventDetailsListener listener) {
        String url = BASE_URL + "/events/" + eventId; // Ex: .../api/events/1

        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.GET,
                url,
                null,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        // 1. Parse do Evento Principal
                        // Precisamos criar um método auxiliar no parser ou fazer aqui à mão.
                        // Como já tens o EventJsonParser, o ideal seria atualizá-lo,
                        // mas para ser rápido, vamos fazer o parse aqui mesmo:
                        try {
                            int id = response.getInt("id");
                            String name = response.getString("name");
                            String description = response.optString("description", "");
                            String start = response.getString("start_date");
                            String end = response.getString("end_date");
                            String status = response.getString("status");

                            Event event = new Event(id, name, description, start, end, status);

                            // 2. Parse das Sessões (que vêm dentro do JSON do evento)
                            ArrayList<Session> sessionsList = new ArrayList<>();
                            if (response.has("sessions")) {
                                JSONArray sessionsArray = response.getJSONArray("sessions");
                                for (int i = 0; i < sessionsArray.length(); i++) {
                                    JSONObject s = sessionsArray.getJSONObject(i);
                                    Session session = new Session(
                                            s.getInt("id"),
                                            s.getString("title"),
                                            s.getString("start_time"),
                                            s.getString("end_time"),
                                            s.optString("location", "TBA"),
                                            s.optInt("capacity", 0)
                                    );
                                    sessionsList.add(session);
                                }
                            }
                            // Guardamos as sessões dentro do evento
                            event.setSessions(sessionsList);

                            if (listener != null) listener.onEventDetailsLoaded(event);

                        } catch (Exception e) {
                            e.printStackTrace();
                            if (listener != null) listener.onError("Erro ao processar dados: " + e.getMessage());
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        if (listener != null) listener.onError("Erro API: " + error.getMessage());
                    }
                }
        ) {
            @Override
            public Map<String, String> getHeaders() {
                Map<String, String> headers = new HashMap<>();
                String token = getAccessToken();
                if (token != null) headers.put("Authorization", "Bearer " + token);
                return headers;
            }
        };
        volleyQueue.add(request);
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