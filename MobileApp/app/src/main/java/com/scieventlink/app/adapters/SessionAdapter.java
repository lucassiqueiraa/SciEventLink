package com.scieventlink.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.scieventlink.app.R;
import com.scieventlink.app.models.Session;
import java.util.ArrayList;

public class SessionAdapter extends RecyclerView.Adapter<SessionAdapter.ViewHolder> {

    private ArrayList<Session> sessions;
    private OnSessionClickListener listener;
    private OnFavoriteClickListener favoriteClickListener;

    public interface OnSessionClickListener {
        void onSessionClick(Session session);
    }

    public interface OnFavoriteClickListener {
        void onFavoriteClick(Session session, int position);
    }

    public void setOnSessionClickListener(OnSessionClickListener listener) {
        this.listener = listener;
    }

    public void setOnFavoriteClickListener(OnFavoriteClickListener listener) {
        this.favoriteClickListener = listener;
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

        // Formatação de hora
        try {
            String start = session.getStartTime().substring(11, 16);
            String end = session.getEndTime().substring(11, 16);
            holder.tvTime.setText(start + " - " + end);
        } catch (Exception e) {
            holder.tvTime.setText(session.getStartTime());
        }

        holder.tvLocation.setText(" | " + session.getLocation());

        // Atualiza ícone de favorito conforme o estado da sessão
        if (session.isFavorite()) {
            holder.ivFavorite.setImageResource(android.R.drawable.btn_star_big_on);
        } else {
            holder.ivFavorite.setImageResource(android.R.drawable.btn_star_big_off);
        }

        holder.itemView.setOnClickListener(v -> {
            if (listener != null) listener.onSessionClick(session);
        });

        // Clique na estrela
        holder.ivFavorite.setOnClickListener(v -> {
            if (favoriteClickListener != null) {
                favoriteClickListener.onFavoriteClick(session, position);
            }
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
        ImageView ivFavorite;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            tvTitle = itemView.findViewById(R.id.tvSessionTitle);
            tvTime = itemView.findViewById(R.id.tvSessionTime);
            tvLocation = itemView.findViewById(R.id.tvSessionLocation);
            ivFavorite = itemView.findViewById(R.id.ivFavorite);
        }
    }
}