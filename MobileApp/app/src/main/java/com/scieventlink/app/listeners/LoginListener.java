package com.scieventlink.app.listeners;

import android.content.Context;

public interface LoginListener {
    void onValidateLogin(String token, String username, Context context);
    void onLoginError(String message);
}