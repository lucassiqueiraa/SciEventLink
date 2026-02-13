package com.scieventlink.app.models;

public class Session {
    private final int id;
    private final String title;
    private final String startTime;
    private final String endTime;
    private final String location;
    private final int capacity;
    private boolean isFavorite;
    private int favoriteId; // ID do registro de favorito na BD

    public Session(int id, String title, String startTime, String endTime, String location, int capacity) {
        this.id = id;
        this.title = title;
        this.startTime = startTime;
        this.endTime = endTime;
        this.location = location;
        this.capacity = capacity;
        this.isFavorite = false;
        this.favoriteId = -1;
    }

    // Getters e Setters
    public int getId() { return id; }
    public String getTitle() { return title; }
    public String getStartTime() { return startTime; }
    public String getEndTime() { return endTime; }
    public String getLocation() { return location; }
    public int getCapacity() { return capacity; }
    
    public boolean isFavorite() { return isFavorite; }
    public void setFavorite(boolean favorite) { isFavorite = favorite; }

    public int getFavoriteId() { return favoriteId; }
    public void setFavoriteId(int favoriteId) { this.favoriteId = favoriteId; }
}