package com.scieventlink.app.utils;

import org.json.JSONException;
import org.json.JSONObject;

public class LoginJsonParser {

    public static String parserJsonLogin(String response) {
        try {
            JSONObject jsonObject = new JSONObject(response);

            return jsonObject.getString("token");

        } catch (JSONException e) {
            e.printStackTrace();
            return null;
        }
    }
}