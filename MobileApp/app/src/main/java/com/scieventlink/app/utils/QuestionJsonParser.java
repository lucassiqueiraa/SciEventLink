package com.scieventlink.app.utils;

import com.scieventlink.app.models.Question;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

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

    public static ArrayList<Question> parseQuestions(String response) {
        ArrayList<Question> questions = new ArrayList<>();
        try {
            JSONArray jsonArray = new JSONArray(response);
            for (int i = 0; i < jsonArray.length(); i++) {
                JSONObject obj = jsonArray.getJSONObject(i);
                questions.add(new Question(
                        obj.getInt("id"),
                        obj.getString("question_text"),
                        obj.getString("created_at"),
                        obj.getString("user_name")
                ));
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return questions;
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
