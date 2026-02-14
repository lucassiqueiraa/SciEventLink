package com.scieventlink.app;

import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;
import com.scieventlink.app.adapters.SessionPagerAdapter;

public class SessionDetailsActivity extends AppCompatActivity {

    private int sessionId;
    private String sessionTitle;
    private String sessionDescription;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_session_details);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setTitle("Sessão");
        }

        sessionId = getIntent().getIntExtra("session_id", -1);
        sessionTitle = getIntent().getStringExtra("session_title");

        sessionDescription = getIntent().getStringExtra("session_description");
        if (sessionDescription == null) {
            sessionDescription = "Sem descrição disponível.";
        }

        TabLayout tabLayout = findViewById(R.id.tabLayout);
        ViewPager2 viewPager = findViewById(R.id.viewPager);

        SessionPagerAdapter adapter = new SessionPagerAdapter(this, sessionId, sessionTitle, sessionDescription);
        viewPager.setAdapter(adapter);

        new TabLayoutMediator(tabLayout, viewPager,
                (tab, position) -> {
                    if (position == 0) tab.setText("Detalhes & Feedback");
                    else tab.setText("Perguntas (Q&A)");
                }
        ).attach();
    }

    @Override
    public boolean onSupportNavigateUp() {
        finish();
        return true;
    }
}