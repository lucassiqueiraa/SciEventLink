package com.scieventlink.app.listeners;

public interface FeedbackListener {
    void onFeedbackSuccess();
    void onFeedbackError(String message);
}
