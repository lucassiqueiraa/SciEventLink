package com.scieventlink.app.adapters;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.scieventlink.app.R;
import com.scieventlink.app.models.Question;

import java.util.ArrayList;

public class QuestionAdapter extends RecyclerView.Adapter<QuestionAdapter.QuestionViewHolder> {

    private ArrayList<Question> questions;

    public QuestionAdapter(ArrayList<Question> questions) {
        this.questions = questions;
    }

    public void updateQuestions(ArrayList<Question> newQuestions) {
        this.questions = newQuestions;
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public QuestionViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_question, parent, false);
        return new QuestionViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull QuestionViewHolder holder, int position) {
        Question question = questions.get(position);
        holder.tvUserName.setText(question.getUserName());
        holder.tvDate.setText(question.getCreatedAt());
        holder.tvQuestionText.setText(question.getQuestionText());
    }

    @Override
    public int getItemCount() {
        return questions != null ? questions.size() : 0;
    }

    static class QuestionViewHolder extends RecyclerView.ViewHolder {
        TextView tvUserName, tvDate, tvQuestionText;

        public QuestionViewHolder(@NonNull View itemView) {
            super(itemView);
            tvUserName = itemView.findViewById(R.id.tvUserName);
            tvDate = itemView.findViewById(R.id.tvDate);
            tvQuestionText = itemView.findViewById(R.id.tvQuestionText);
        }
    }
}
