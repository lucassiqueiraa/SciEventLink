package com.scieventlink.app.models;

public class Session {
    private final int id;
    private final String title;
    private final String startTime;
    private final String endTime;
    private final String location;
    private final int capacity;

    public Session(int id, String title, String startTime, String endTime, String location, int capacity) {
        this.id = id;
        this.title = title;
        this.startTime = startTime;
        this.endTime = endTime;
        this.location = location;
        this.capacity = capacity;
    }

    // Getters
    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getStartTime() { return startTime; }
    public String getEndTime() { return endTime; }
    public String getLocation() { return location; }
    public int getCapacity() { return capacity; }
}