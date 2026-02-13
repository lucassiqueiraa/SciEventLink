package com.scieventlink.app.listeners;

import com.scieventlink.app.models.Session;
import java.util.ArrayList;

public interface FavoriteListener {
    void onFavoriteChanged(int sessionId, boolean isFavorite);
    void onFavoritesLoaded(ArrayList<Session> favoriteSessions);
    void onError(String message);
}