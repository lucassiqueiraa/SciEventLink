package com.scieventlink.app.utils;

import com.scieventlink.app.models.Ticket;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;

public class TicketJsonParser {
    public static ArrayList<Ticket> parseTickets(String response) {
        ArrayList<Ticket> tickets = new ArrayList<>();
        try {
            JSONArray jsonArray = new JSONArray(response);
            for (int i = 0; i < jsonArray.length(); i++) {
                JSONObject obj = jsonArray.getJSONObject(i);
                tickets.add(new Ticket(
                        obj.getInt("id"),
                        obj.getString("event_name"),
                        obj.getString("event_date"),
                        obj.getString("location"),
                        obj.getString("qr_data"),
                        obj.getString("status"),
                        obj.getString("ticket_type"),
                        obj.getString("price")
                ));
            }
        } catch (JSONException e) {
            e.printStackTrace();
        }
        return tickets;
    }
}
