package com.scieventlink.app.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.scieventlink.app.R;
import com.scieventlink.app.SessionDetailsActivity;
import com.scieventlink.app.adapters.SessionAdapter;
import com.scieventlink.app.listeners.FavoriteListener;
import com.scieventlink.app.models.Session;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class FavoritesFragment extends Fragment {

    private RecyclerView rvFavorites;
    private SessionAdapter adapter;

    public FavoritesFragment() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_favorites, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        rvFavorites = view.findViewById(R.id.rvFavorites);
        rvFavorites.setLayoutManager(new LinearLayoutManager(getContext()));

        adapter = new SessionAdapter(new ArrayList<>());
        rvFavorites.setAdapter(adapter);

        adapter.setOnItemClickListener(session -> {
            Intent intent = new Intent(getActivity(), SessionDetailsActivity.class);
            intent.putExtra("session_id", session.getId());
            intent.putExtra("session_title", session.getTitle());

            String dynamicDesc = "Sessão agendada para as " + session.getStartTime() + 
                               " no local: " + session.getLocation() + 
                               ". Capacidade: " + session.getCapacity() + " pessoas.";
            
            intent.putExtra("session_description", dynamicDesc);
            startActivity(intent);
        });

        adapter.setOnFavoriteClickListener((session, position) -> {
            SingletonManager.getInstance(getContext()).toggleFavorite(session, new FavoriteListener() {
                @Override
                public void onFavoritesLoaded(ArrayList<Session> favorites) {
                }

                @Override
                public void onFavoriteChanged(int sessionId, boolean isFavorite) {
                    if (!isFavorite) {
                        loadFavorites();
                    }
                }

                @Override
                public void onError(String message) {
                    Toast.makeText(getContext(), message, Toast.LENGTH_SHORT).show();
                }
            });
        });

        loadFavorites();
    }

    private void loadFavorites() {
        SingletonManager.getInstance(getContext()).getFavorites(new FavoriteListener() {
            @Override
            public void onFavoritesLoaded(ArrayList<Session> favorites) {
                if (adapter != null) {
                    adapter.updateSessions(favorites);
                }
            }

            @Override
            public void onFavoriteChanged(int sessionId, boolean isFavorite) {
            }

            @Override
            public void onError(String message) {
                if (getContext() != null) {
                    Toast.makeText(getContext(), "Erro: " + message, Toast.LENGTH_SHORT).show();
                }
            }
        });
    }
}