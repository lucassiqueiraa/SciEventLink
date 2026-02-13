package com.scieventlink.app.utils;

import com.scieventlink.app.models.Session;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;

public class FavoritesJsonParser {

    public static ArrayList<Session> parserFavorites(String jsonResponse) {
        ArrayList<Session> favorites = new ArrayList<>();
        try {
            JSONArray array = new JSONArray(jsonResponse);
            for (int i = 0; i < array.length(); i++) {
                JSONObject obj = array.getJSONObject(i);
                Session session = parseSession(obj);
                if (session != null) favorites.add(session);
            }
        } catch (JSONException e) { e.printStackTrace(); }
        return favorites;
    }

    public static int parserAddFavoriteResponse(String jsonResponse) {
        try {
            JSONObject response = new JSONObject(jsonResponse);
            if (response.has("id")) return response.getInt("id");
            if (response.has("id_favorito")) return response.getInt("id_favorito");
            if (response.has("session_id")) return response.getInt("session_id"); // Fallback
        } catch (JSONException e) { e.printStackTrace(); }
        return -1;
    }

    private static Session parseSession(JSONObject s) {
        try {
            int id = s.has("session_id") ? s.getInt("session_id") : s.optInt("id", -1);
            Session session = new Session(
                    id,
                    s.getString("title"),
                    s.optString("start_time", "N/A"),
                    s.optString("end_time", "N/A"),
                    s.optString("location", "TBA"),
                    s.optInt("capacity", 0)
            );
            session.setFavorite(true);
            session.setFavoriteId(s.optInt("id", id));
            return session;
        } catch (JSONException e) { return null; }
    }
}