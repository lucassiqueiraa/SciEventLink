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

// Removemos o import do AdapterView que estava a causar conflito

public class SessionAdapter extends RecyclerView.Adapter<SessionAdapter.ViewHolder> {

    private ArrayList<Session> sessions;

    private OnItemClickListener itemListener;
    private OnFavoriteClickListener favoriteClickListener;

    public SessionAdapter(ArrayList<Session> sessions) {
        this.sessions = sessions;
    }

    // --- INTERFACES ---

    public interface OnItemClickListener {
        void onItemClick(Session session);
    }

    // Interface para clicar no coração (Favoritos)
    public interface OnFavoriteClickListener {
        void onFavoriteClick(Session session, int position);
    }

    // --- SETTERS ---

    public void setOnItemClickListener(OnItemClickListener listener) {
        this.itemListener = listener;
    }

    public void setOnFavoriteClickListener(OnFavoriteClickListener listener) {
        this.favoriteClickListener = listener;
    }

    // --- MÉTODOS DO ADAPTER ---

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_session, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        Session session = sessions.get(position);

        // Preencher dados
        holder.tvTitle.setText(session.getTitle());
        holder.tvLocation.setText(" | " + session.getLocation());

        try {
            String start = session.getStartTime().length() > 16 ? session.getStartTime().substring(11, 16) : session.getStartTime();
            String end = session.getEndTime().length() > 16 ? session.getEndTime().substring(11, 16) : session.getEndTime();
            holder.tvTime.setText(start + " - " + end);
        } catch (Exception e) {
            holder.tvTime.setText(session.getStartTime());
        }

        if (session.isFavorite()) {
            holder.ivFavorite.setImageResource(android.R.drawable.btn_star_big_on);
        } else {
            holder.ivFavorite.setImageResource(android.R.drawable.btn_star_big_off);
        }

        holder.itemView.setOnClickListener(v -> {
            if (itemListener != null) {
                itemListener.onItemClick(session);
            }
        });

        holder.ivFavorite.setOnClickListener(v -> {
            if (favoriteClickListener != null) {
                favoriteClickListener.onFavoriteClick(session, holder.getAdapterPosition());
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