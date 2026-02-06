package com.scieventlink.app.utils;

import com.scieventlink.app.models.Event;
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
}