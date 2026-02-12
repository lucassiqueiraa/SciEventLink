package com.scieventlink.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.scieventlink.app.R;
import com.scieventlink.app.models.Session;
import java.util.ArrayList;

public class SessionAdapter extends RecyclerView.Adapter<SessionAdapter.ViewHolder> {

    private ArrayList<Session> sessions;
    private OnSessionClickListener listener;

    // Interface para clique na sessão (para o futuro Nível 3)
    public interface OnSessionClickListener {
        void onSessionClick(Session session);
    }

    public void setOnSessionClickListener(OnSessionClickListener listener) {
        this.listener = listener;
    }

    public SessionAdapter(ArrayList<Session> sessions) {
        this.sessions = sessions;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_session, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        Session session = sessions.get(position);
        holder.tvTitle.setText(session.getTitle());
        // Ajusta aqui conforme os teus Getters na classe Session
        holder.tvTime.setText(session.getStartTime().substring(11, 16) + " - " + session.getEndTime().substring(11, 16));
        holder.tvLocation.setText(" | " + session.getLocation());

        holder.itemView.setOnClickListener(v -> {
            if (listener != null) listener.onSessionClick(session);
        });
    }

    @Override
    public int getItemCount() {
        return sessions != null ? sessions.size() : 0;
    }

    public void updateSessions(ArrayList<Session> newSessions) {
        this.sessions = newSessions;
        notifyDataSetChanged();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvTitle, tvTime, tvLocation;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            tvTitle = itemView.findViewById(R.id.tvSessionTitle);
            tvTime = itemView.findViewById(R.id.tvSessionTime);
            tvLocation = itemView.findViewById(R.id.tvSessionLocation);
        }
    }
}