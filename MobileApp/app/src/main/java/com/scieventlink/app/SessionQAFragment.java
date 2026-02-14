package com.scieventlink.app;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.scieventlink.app.R;
import com.scieventlink.app.adapters.QuestionAdapter;
import com.scieventlink.app.listeners.QuestionListener;
import com.scieventlink.app.models.Question;
import com.scieventlink.app.models.SingletonManager;

import java.util.ArrayList;

public class SessionQAFragment extends Fragment {

    private int sessionId;
    private QuestionAdapter adapter;

    public SessionQAFragment() {
    }

    public static SessionQAFragment newInstance(int id) {
        SessionQAFragment fragment = new SessionQAFragment();
        Bundle args = new Bundle();
        args.putInt("id", id);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            sessionId = getArguments().getInt("id");
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_session_q_a, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        RecyclerView rvQuestions = view.findViewById(R.id.rvQuestions);
        rvQuestions.setLayoutManager(new LinearLayoutManager(getContext()));
        
        adapter = new QuestionAdapter(new ArrayList<>());
        rvQuestions.setAdapter(adapter);

        loadQuestions();

        EditText etQuestion = view.findViewById(R.id.etQuestion);
        ImageButton btnSend = view.findViewById(R.id.btnSendQuestion);

        btnSend.setOnClickListener(v -> {
            String text = etQuestion.getText().toString().trim();
            if (text.isEmpty()) {
                Toast.makeText(getContext(), "Escreva uma pergunta primeiro.", Toast.LENGTH_SHORT).show();
                return;
            }

            btnSend.setEnabled(false);

            SingletonManager.getInstance(getContext()).sendSessionQuestion(sessionId, text, new QuestionListener() {
                @Override
                public void onQuestionSuccess() {
                    if (getContext() == null) return;
                    Toast.makeText(getContext(), "Pergunta enviada!", Toast.LENGTH_SHORT).show();
                    etQuestion.setText("");
                    btnSend.setEnabled(true);
                    loadQuestions();
                }

                @Override
                public void onQuestionError(String message) {
                    if (getContext() == null) return;
                    Toast.makeText(getContext(), message, Toast.LENGTH_LONG).show();
                    btnSend.setEnabled(true);
                }
            });
        });
    }

    private void loadQuestions() {
        SingletonManager.getInstance(getContext()).getSessionQuestions(sessionId, new SingletonManager.QuestionsListListener() {
            @Override
            public void onQuestionsLoaded(ArrayList<Question> questions) {
                if (adapter != null) {
                    adapter.updateQuestions(questions);
                }
            }

            @Override
            public void onError(String message) {
                if (getContext() != null) {
                    Toast.makeText(getContext(), "Erro ao carregar perguntas: " + message, Toast.LENGTH_SHORT).show();
                }
            }
        });
    }
}