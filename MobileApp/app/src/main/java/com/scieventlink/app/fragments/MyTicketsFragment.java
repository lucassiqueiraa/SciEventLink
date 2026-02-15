package com.scieventlink.app.fragments;

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
import com.scieventlink.app.adapters.TicketAdapter;
import com.scieventlink.app.listeners.TicketListener;
import com.scieventlink.app.models.Ticket;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class MyTicketsFragment extends Fragment {

    private RecyclerView rvTickets;
    private TicketAdapter adapter;

    public MyTicketsFragment() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_my_tickets, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        rvTickets = view.findViewById(R.id.rvMyTickets);
        rvTickets.setLayoutManager(new LinearLayoutManager(getContext()));

        adapter = new TicketAdapter(new ArrayList<>());
        rvTickets.setAdapter(adapter);

        loadTickets();
    }

    private void loadTickets() {
        SingletonManager.getInstance(getContext()).getMyTickets(new TicketListener() {
            @Override
            public void onTicketsLoaded(ArrayList<Ticket> tickets) {
                if (adapter != null) {
                    adapter.updateTickets(tickets);
                }
            }

            @Override
            public void onError(String message) {
                if (getContext() != null) {
                    Toast.makeText(getContext(), "Erro ao carregar bilhetes: " + message, Toast.LENGTH_SHORT).show();
                }
            }
        });
    }
}