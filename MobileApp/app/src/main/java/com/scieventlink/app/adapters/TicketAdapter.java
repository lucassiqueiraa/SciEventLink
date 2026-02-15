package com.scieventlink.app.adapters;

import android.graphics.Bitmap;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.google.zxing.BarcodeFormat;
import com.google.zxing.WriterException;
import com.journeyapps.barcodescanner.BarcodeEncoder;
import com.scieventlink.app.R;
import com.scieventlink.app.models.Ticket;
import java.util.ArrayList;

public class TicketAdapter extends RecyclerView.Adapter<TicketAdapter.TicketViewHolder> {

    private ArrayList<Ticket> tickets;

    public TicketAdapter(ArrayList<Ticket> tickets) {
        this.tickets = tickets;
    }

    public void updateTickets(ArrayList<Ticket> newTickets) {
        this.tickets = newTickets;
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public TicketViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_ticket, parent, false);
        return new TicketViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull TicketViewHolder holder, int position) {
        Ticket ticket = tickets.get(position);

        holder.tvEventName.setText(ticket.getEventName());
        holder.tvDate.setText(ticket.getEventDate());
        holder.tvTypePrice.setText(ticket.getTicketType() + " - " + ticket.getPrice() + "€");
        holder.tvStatus.setText(ticket.getStatus().toUpperCase());

        // Geração do QR Code dinâmico
        try {
            BarcodeEncoder barcodeEncoder = new BarcodeEncoder();
            Bitmap bitmap = barcodeEncoder.encodeBitmap(ticket.getQrData(), BarcodeFormat.QR_CODE, 400, 400);
            holder.ivQrCode.setImageBitmap(bitmap);
        } catch (WriterException e) {
            e.printStackTrace();
        }

        // Lógica visual para bilhetes UTILIZADOS (UX)
        if ("used".equalsIgnoreCase(ticket.getStatus())) {
            holder.itemView.setAlpha(0.5f); // Meio transparente
            holder.tvStatus.setText("UTILIZADO");
            holder.tvStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(Color.GRAY));
        } else {
            holder.itemView.setAlpha(1.0f);
            holder.tvStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(holder.itemView.getContext().getColor(R.color.success)));
        }
    }

    @Override
    public int getItemCount() {
        return tickets != null ? tickets.size() : 0;
    }

    static class TicketViewHolder extends RecyclerView.ViewHolder {
        TextView tvEventName, tvDate, tvTypePrice, tvStatus;
        ImageView ivQrCode;

        public TicketViewHolder(@NonNull View itemView) {
            super(itemView);
            tvEventName = itemView.findViewById(R.id.tvTicketEventName);
            tvDate = itemView.findViewById(R.id.tvTicketDate);
            tvTypePrice = itemView.findViewById(R.id.tvTicketTypePrice);
            tvStatus = itemView.findViewById(R.id.tvTicketStatus);
            ivQrCode = itemView.findViewById(R.id.ivQrCode);
        }
    }
}
