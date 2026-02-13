package com.scieventlink.app;

import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.scieventlink.app.adapters.SessionAdapter;
import com.scieventlink.app.listeners.FavoriteListener;
import com.scieventlink.app.models.Event;
import com.scieventlink.app.models.Session;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class EventDetailsActivity extends AppCompatActivity {

    private TextView tvName, tvDate, tvDesc;
    private RecyclerView rvSessions;
    private SessionAdapter adapter;
    private int eventId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_event_details);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
            getSupportActionBar().setTitle("Detalhes do Evento");
        }

        eventId = getIntent().getIntExtra("event_id", -1);

        if (eventId == -1) {
            Toast.makeText(this, "Erro: Evento inválido", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        tvName = findViewById(R.id.tvDetailName);
        tvDate = findViewById(R.id.tvDetailDate);
        tvDesc = findViewById(R.id.tvDetailDesc);
        rvSessions = findViewById(R.id.rvSessions);

        rvSessions.setLayoutManager(new LinearLayoutManager(this));
        adapter = new SessionAdapter(new ArrayList<>());
        rvSessions.setAdapter(adapter);

        adapter.setOnFavoriteClickListener(new SessionAdapter.OnFavoriteClickListener() {
            @Override
            public void onFavoriteClick(Session session, int position) {
                SingletonManager.getInstance(EventDetailsActivity.this).toggleFavorite(session, new FavoriteListener() {
                    @Override
                    public void onFavoriteChanged(int sessionId, boolean isFavorite) {
                        adapter.notifyItemChanged(position);
                        String msg = isFavorite ? "Sessão favoritada!" : "Removida dos favoritos.";
                        Toast.makeText(EventDetailsActivity.this, msg, Toast.LENGTH_SHORT).show();
                    }

                    @Override
                    public void onFavoritesLoaded(ArrayList<Session> favoriteSessions) {}

                    @Override
                    public void onError(String message) {
                        Toast.makeText(EventDetailsActivity.this, "Erro: " + message, Toast.LENGTH_SHORT).show();
                    }
                });
            }
        });

        carregarDadosCompletos();
    }

    private void carregarDadosCompletos() {
        SingletonManager singleton = SingletonManager.getInstance(this);

        singleton.getFavorites(new FavoriteListener() {
            @Override
            public void onFavoritesLoaded(ArrayList<Session> favoriteSessions) {
                singleton.getEventDetails(eventId, new SingletonManager.EventDetailsListener() {
                    @Override
                    public void onEventDetailsLoaded(Event event) {
                        // O SEGREDO ESTÁ AQUI: Cruzar antes de dar o updateSessions
                        cruzarDadosSessoes(event.getSessions(), favoriteSessions);

                        tvName.setText(event.getName());
                        tvDate.setText("De " + event.getStartDate() + " até " + event.getEndDate());
                        tvDesc.setText(event.getDescription());
                        adapter.updateSessions(event.getSessions()); // UI atualiza com as estrelas marcadas
                    }
                    @Override public void onError(String message) { /* ... */ }
                });
            }
            @Override public void onFavoriteChanged(int s, boolean i) {}
            @Override public void onError(String m) {
                // Se favoritos falhar, carrega o evento puro
                singleton.getEventDetails(eventId, new SingletonManager.EventDetailsListener() {
                    @Override public void onEventDetailsLoaded(Event e) { adapter.updateSessions(e.getSessions()); }
                    @Override public void onError(String m2) {}
                });
            }
        });
    }

    private void cruzarDadosSessoes(ArrayList<Session> sessoesEvento, ArrayList<Session> favoritosApi) {
        for (Session s : sessoesEvento) {
            s.setFavorite(false);
            s.setFavoriteId(-1);

            for (Session fav : favoritosApi) {
                // Compara o "id" do JSON de Eventos com o "session_id" do JSON de Favoritos
                if (s.getId() == fav.getId()) {
                    s.setFavorite(true);
                    s.setFavoriteId(fav.getFavoriteId()); // Garante que temos o ID para o clique
                    break;
                }
            }
        }
    }

    @Override
    public boolean onOptionsItemSelected(@NonNull MenuItem item) {
        if (item.getItemId() == android.R.id.home) {
            onBackPressed();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }
}