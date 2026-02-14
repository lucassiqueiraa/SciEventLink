package com.scieventlink.app.models;

public class Question {
    private int id;
    private String questionText;
    private String createdAt;
    private String userName;

    public Question(int id, String questionText, String createdAt, String userName) {
        this.id = id;
        this.questionText = questionText;
        this.createdAt = createdAt;
        this.userName = userName;
    }

    public int getId() { return id; }
    public String getQuestionText() { return questionText; }
    public String getCreatedAt() { return createdAt; }
    public String getUserName() { return userName; }
}
