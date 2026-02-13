package com.scieventlink.app.utils;

import com.scieventlink.app.models.Event;
import com.scieventlink.app.models.Session;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;

public class EventJsonParser {

    public static ArrayList<Event> parserJsonEvents(String response) {
        ArrayList<Event> events = new ArrayList<>();
        try {
            JSONArray jsonArray = new JSONArray(response);

            for (int i = 0; i < jsonArray.length(); i++) {
                JSONObject jsonObject = jsonArray.getJSONObject(i);

                int id = jsonObject.getInt("id");
                String name = jsonObject.getString("name");
                String description = jsonObject.optString("description", "Sem descrição");

                String startDate = jsonObject.getString("start_date");
                String endDate = jsonObject.getString("end_date");
                String status = jsonObject.getString("status");

                Event event = new Event(id, name, description, startDate, endDate, status);
                events.add(event);
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return events;
    }

    public static Event parserEventDetails(String jsonResponse) {
        try {
            JSONObject response = new JSONObject(jsonResponse);
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
                    JSONObject s = arr.getJSONObject(i);
                    Session session = new Session(
                            s.getInt("id"),
                            s.getString("title"),
                            s.optString("start_time", "N/A"),
                            s.optString("end_time", "N/A"),
                            s.optString("location", "TBA"),
                            s.optInt("capacity", 0)
                    );
                    session.setFavorite(s.optBoolean("is_favorite", false));
                    sessions.add(session);
                }
            }
            event.setSessions(sessions);
            return event;
        } catch (Exception e) { return null; }
    }
}