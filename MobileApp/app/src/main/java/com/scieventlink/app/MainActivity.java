package com.scieventlink.app;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.scieventlink.app.adapters.EventAdapter;
import com.scieventlink.app.models.Event;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class MainActivity extends AppCompatActivity {

    private RecyclerView rvEvents;
    private EventAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // 1. Configurar a Lista (RecyclerView)
        rvEvents = findViewById(R.id.rvEvents);
        rvEvents.setLayoutManager(new LinearLayoutManager(this));

        // Inicializa o adaptador vazio (Lembra-te: removemos o 'this' do construtor!)
        adapter = new EventAdapter(new ArrayList<>());
        rvEvents.setAdapter(adapter);

        // 2. Pedir os dados ao "Cozinheiro" (Singleton)
        carregarEventos();

        // 3. Configurar o clique (para a próxima tarefa)
        adapter.setOnItemClickListener(new EventAdapter.OnItemClickListener() {
            @Override
            public void onItemClick(Event event) {
                Intent intent = new Intent(MainActivity.this, EventDetailsActivity.class);
                intent.putExtra("event_id", event.getId());
                startActivity(intent);
            }
        });
    }

    private void carregarEventos() {
        // Verificação de Segurança: Temos token?
        if (SingletonManager.getInstance(this).getAccessToken() == null) {
            Intent intent = new Intent(this, LoginActivity.class);
            startActivity(intent);
            finish();
            return;
        }

        // --- A MAGIA ACONTECE AQUI ---
        // Chamamos o método do Singleton e passamos o nosso "Pager" (Listener)
        SingletonManager.getInstance(this).getAllEvents(new SingletonManager.EventsListener() {

            @Override
            public void onEventsLoaded(ArrayList<Event> events) {
                // SUCESSO! O Pager vibrou com dados.
                // Verificamos se a lista está vazia
                if (events.isEmpty()) {
                    Toast.makeText(MainActivity.this, "Não há eventos disponíveis.", Toast.LENGTH_SHORT).show();
                }
                // Mandamos os dados para o Adaptador desenhar na tela
                adapter.updateEvents(events);
            }

            @Override
            public void onEventsError(String message) {
                // ERRO! O Pager vibrou com más notícias.
                Toast.makeText(MainActivity.this, "Erro: " + message, Toast.LENGTH_LONG).show();
            }
        });
    }
}