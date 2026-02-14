package com.scieventlink.app.utils;

import org.json.JSONException;
import org.json.JSONObject;

public class FeedbackJsonParser {

    public static JSONObject prepareFeedbackJson(int sessionId, int rating, String comment) {
        JSONObject jsonBody = new JSONObject();
        try {
            jsonBody.put("session_id", sessionId);
            jsonBody.put("rating", rating);
            jsonBody.put("comment", comment);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return jsonBody;
    }

    public static String parseFeedbackError(String response) {
        try {
            JSONObject jsonObject = new JSONObject(response);
            if (jsonObject.has("errors")) {
                JSONObject errors = jsonObject.getJSONObject("errors");
                if (errors.has("session_id")) {
                    return "Já submeteu feedback para esta sessão.";
                }
            }
            return jsonObject.optString("message", "Erro ao processar feedback.");
        } catch (JSONException e) {
            return "Erro desconhecido no servidor.";
        }
    }
}
