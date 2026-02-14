package com.scieventlink.app;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.RatingBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import com.google.android.material.textfield.TextInputEditText;
import com.scieventlink.app.listeners.FeedbackListener;
import com.scieventlink.app.models.SingletonManager;

public class SessionInfoFragment extends Fragment {

    private int sessionId;
    private String title;
    private String desc;

    public SessionInfoFragment() {
    }

    public static SessionInfoFragment newInstance(int id, String title, String desc) {
        SessionInfoFragment fragment = new SessionInfoFragment();
        Bundle args = new Bundle();
        args.putInt("id", id);
        args.putString("title", title);
        args.putString("desc", desc);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            sessionId = getArguments().getInt("id");
            title = getArguments().getString("title");
            desc = getArguments().getString("desc");
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_session_info, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        TextView tvTitle = view.findViewById(R.id.tvSessionTitle);
        TextView tvDesc = view.findViewById(R.id.tvSessionDesc);
        RatingBar ratingBar = view.findViewById(R.id.ratingBar);
        TextInputEditText etComment = view.findViewById(R.id.etComment);
        Button btnSubmit = view.findViewById(R.id.btnSubmit);

        tvTitle.setText(title);
        tvDesc.setText(desc != null ? desc : "Sem descrição disponível.");

        btnSubmit.setOnClickListener(v -> {
            int rating = (int) ratingBar.getRating();
            String comment = etComment.getText().toString();

            if (rating < 1) {
                Toast.makeText(getContext(), "Selecione pelo menos 1 estrela.", Toast.LENGTH_SHORT).show();
                return;
            }

            btnSubmit.setEnabled(false);

            SingletonManager.getInstance(getContext()).sendSessionFeedback(sessionId, rating, comment, new FeedbackListener() {
                @Override
                public void onFeedbackSuccess() {
                    if (getContext() == null) return;
                    Toast.makeText(getContext(), "Feedback enviado com sucesso!", Toast.LENGTH_SHORT).show();

                    ratingBar.setRating(0);
                    etComment.setText("");

                    ratingBar.setEnabled(false);
                    etComment.setEnabled(false);
                    btnSubmit.setText("Feedback Submetido");
                }

                @Override
                public void onFeedbackError(String message) {
                    if (getContext() == null) return;
                    Toast.makeText(getContext(), message, Toast.LENGTH_LONG).show();

                    if (message.contains("Já submeteu")) {
                        ratingBar.setEnabled(false);
                        etComment.setEnabled(false);
                        btnSubmit.setText("Feedback já efetuado");
                    } else {
                        btnSubmit.setEnabled(true);
                    }
                }
            });
        });
    }
}