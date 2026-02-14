package com.scieventlink.app;

import android.content.Intent;
import android.os.Bundle;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.OnBackPressedCallback;
import androidx.annotation.NonNull;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.navigation.NavigationView;
import com.scieventlink.app.adapters.EventAdapter;
import com.scieventlink.app.models.Event;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class MainActivity extends AppCompatActivity implements NavigationView.OnNavigationItemSelectedListener {

    private RecyclerView rvEvents;
    private EventAdapter adapter;

    private DrawerLayout drawerLayout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // 1. Configurar Toolbar
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        // 2. Configurar Drawer e Navigation
        drawerLayout = findViewById(R.id.drawerLayout);
        NavigationView navigationView = findViewById(R.id.navView);
        navigationView.setNavigationItemSelectedListener(this);

        ActionBarDrawerToggle toggle = new ActionBarDrawerToggle(
                this, drawerLayout, toolbar,
                R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawerLayout.addDrawerListener(toggle);
        toggle.syncState();

        getOnBackPressedDispatcher().addCallback(this, new OnBackPressedCallback(true) {
            @Override
            public void handleOnBackPressed() {
                if (drawerLayout.isDrawerOpen(GravityCompat.START)) {
                    drawerLayout.closeDrawer(GravityCompat.START);
                } else {
                    setEnabled(false);
                    getOnBackPressedDispatcher().onBackPressed();
                }
            }
        });

        try {
            View headerView = navigationView.getHeaderView(0);
            TextView tvUsername = headerView.findViewById(R.id.tvUsername);

            String username = SingletonManager.getInstance(this).getUsername();

            if (username != null) {
                tvUsername.setText(username);
            } else {
                tvUsername.setText("Visitante");
            }
        } catch (Exception e) {
            android.util.Log.e("MainActivity", "Erro ao configurar cabeçalho: " + e.getMessage());
        }
        // ----------------------------------

        // 4. Configurar Lista
        rvEvents = findViewById(R.id.rvEvents);
        rvEvents.setLayoutManager(new LinearLayoutManager(this));

        // Inicializar adaptador VAZIO primeiro para evitar erro
        adapter = new EventAdapter(new ArrayList<>());
        rvEvents.setAdapter(adapter);

        // 5. Carregar dados
        carregarEventos();

        adapter.setOnItemClickListener(new EventAdapter.OnItemClickListener() {
            @Override
            public void onItemClick(Event event) {
                Intent intent = new Intent(MainActivity.this, EventDetailsActivity.class);
                intent.putExtra("event_id", event.getId());
                startActivity(intent);
            }
        });
    }

    @Override
    public boolean onNavigationItemSelected(@NonNull MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.nav_eventos) {
        } else if (id == R.id.nav_bilhetes) {
            Toast.makeText(this, "Bilhetes em breve...", Toast.LENGTH_SHORT).show();
        } else if (id == R.id.nav_logout) {
            SingletonManager.getInstance(this).setAccessToken(null);
            Intent intent = new Intent(this, LoginActivity.class);
            startActivity(intent);
            finish();
        }

        drawerLayout.closeDrawer(GravityCompat.START);
        return true;
    }


    private void carregarEventos() {
        if (SingletonManager.getInstance(this).getAccessToken() == null) {
            Intent intent = new Intent(this, LoginActivity.class);
            startActivity(intent);
            finish();
            return;
        }

        SingletonManager.getInstance(this).getAllEvents(new SingletonManager.EventsListener() {
            @Override
            public void onEventsLoaded(ArrayList<Event> events) {
                if (events.isEmpty()) {
                    Toast.makeText(MainActivity.this, "Não há eventos.", Toast.LENGTH_SHORT).show();
                }
                adapter.updateEvents(events);
            }

            @Override
            public void onEventsError(String message) {
                Toast.makeText(MainActivity.this, "Erro: " + message, Toast.LENGTH_LONG).show();
            }
        });
    }
}