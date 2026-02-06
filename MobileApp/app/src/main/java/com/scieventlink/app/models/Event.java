package com.scieventlink.app.models;

import java.util.ArrayList;

public class Event {
    private final int id;
    private final String name;
    private final String description;
    private final String startDate;
    private final String endDate;
    private final String status;
    private ArrayList<Session> sessions;

    public Event(int id, String name, String description, String startDate, String endDate, String status) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.startDate = startDate;
        this.endDate = endDate;
        this.status = status;
        this.sessions = new ArrayList<>();
    }

    // Getters
    public int getId() { return id; }
    public String getName() { return name; }
    public String getDescription() { return description; }
    public String getStartDate() { return startDate; }
    public String getEndDate() { return endDate; }
    public String getStatus() { return status; }

    public ArrayList<Session> getSessions() { return sessions; }
    public void setSessions(ArrayList<Session> sessions) { this.sessions = sessions; }
}