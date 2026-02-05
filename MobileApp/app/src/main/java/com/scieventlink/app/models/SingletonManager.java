package com.scieventlink.app.models;

import android.content.Context;
import android.content.SharedPreferences;
import android.util.Log;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.scieventlink.app.listeners.LoginListener;
import com.scieventlink.app.utils.LoginJsonParser;

import org.json.JSONObject;

public class SingletonManager {

    private static SingletonManager instance = null;
    private static RequestQueue volleyQueue = null;
    private static final String BASE_URL = "http://172.22.21.248/scieventlink/WebApp/backend/web/api";

    private LoginListener loginListener;
    private Context context;

    // Chaves para guardar dados
    private static final String PREF_NAME = "SciEventLinkPrefs";
    private static final String KEY_TOKEN = "auth_token";
    private static final String KEY_USERNAME = "username";

    public static synchronized SingletonManager getInstance(Context context) {
        if (instance == null) {
            instance = new SingletonManager(context);
        }
        return instance;
    }

    private SingletonManager(Context context) {
        this.context = context;
        volleyQueue = Volley.newRequestQueue(context);
    }

    // Método para a Activity se registar e ouvir as respostas
    public void setLoginListener(LoginListener listener) {
        this.loginListener = listener;
    }

    // --- MÉTODOS DE API ---

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
                            if (loginListener != null) loginListener.onLoginError("Erro: Token não encontrado na resposta.");
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        String msg = "Erro de conexão";

                        if (error.networkResponse != null) {
                            Log.e("API_LOGIN", "Erro Code: " + error.networkResponse.statusCode);
                            if (error.networkResponse.statusCode == 401) {
                                msg = "Credenciais inválidas (401)";
                            } else if (error.networkResponse.statusCode == 404) {
                                msg = "Endpoint não encontrado (404). Verifique a URL.";
                            }
                        } else {
                            Log.e("API_LOGIN", "Erro Volley: " + error.getMessage());
                        }

                        if (loginListener != null) {
                            loginListener.onLoginError(msg);
                        }
                    }
                }
        );

        volleyQueue.add(request);
    }

    // Método auxiliar para pegar o token guardado
    public String getAccessToken() {
        SharedPreferences prefs = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return prefs.getString(KEY_TOKEN, null);
    }
}