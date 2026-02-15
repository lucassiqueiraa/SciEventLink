package com.scieventlink.app.listeners;

import com.scieventlink.app.models.Ticket;
import java.util.ArrayList;

public interface TicketListener {
    void onTicketsLoaded(ArrayList<Ticket> tickets);
    void onError(String message);
}
