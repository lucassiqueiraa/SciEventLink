package com.scieventlink.app.utils;

import org.json.JSONException;
import org.json.JSONObject;

public class QuestionJsonParser {

    public static JSONObject prepareQuestionJson(int sessionId, String questionText) {
        JSONObject jsonBody = new JSONObject();
        try {
            jsonBody.put("session_id", sessionId);
            jsonBody.put("question_text", questionText);
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return jsonBody;
    }

    public static String parseQuestionError(String response) {
        try {
            JSONObject jsonObject = new JSONObject(response);
            return jsonObject.optString("message", "Erro ao processar a pergunta.");
        } catch (JSONException e) {
            return "Erro desconhecido no servidor.";
        }
    }
}
