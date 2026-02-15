package com.scieventlink.app.models;

public class Ticket {
    private int id;
    private String eventName;
    private String eventDate;
    private String location;
    private String qrData;
    private String status;
    private String ticketType;
    private String price;

    public Ticket(int id, String eventName, String eventDate, String location, String qrData, String status, String ticketType, String price) {
        this.id = id;
        this.eventName = eventName;
        this.eventDate = eventDate;
        this.location = location;
        this.qrData = qrData;
        this.status = status;
        this.ticketType = ticketType;
        this.price = price;
    }

    public int getId() { return id; }
    public String getEventName() { return eventName; }
    public String getEventDate() { return eventDate; }
    public String getLocation() { return location; }
    public String getQrData() { return qrData; }
    public String getStatus() { return status; }
    public String getTicketType() { return ticketType; }
    public String getPrice() { return price; }
}
