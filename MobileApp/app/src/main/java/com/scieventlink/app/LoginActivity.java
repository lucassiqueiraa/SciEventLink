package com.scieventlink.app;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.textfield.TextInputEditText;

import com.scieventlink.app.listeners.LoginListener;
import com.scieventlink.app.models.SingletonManager;

public class LoginActivity extends AppCompatActivity implements LoginListener {

    private TextInputEditText etUsername, etPassword;
    private Button btnLogin;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        SingletonManager.getInstance(getApplicationContext()).setLoginListener(this);

        etUsername = findViewById(R.id.etUsername);
        etPassword = findViewById(R.id.etPassword);
        btnLogin = findViewById(R.id.btnLogin);

        btnLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String user = etUsername.getText() != null ? etUsername.getText().toString().trim() : "";
                String pass = etPassword.getText() != null ? etPassword.getText().toString().trim() : "";

                if (!user.isEmpty() && !pass.isEmpty()) {
                    Toast.makeText(LoginActivity.this, "A contactar servidor...", Toast.LENGTH_SHORT).show();

                    SingletonManager.getInstance(getApplicationContext())
                            .loginAPI(user, pass, getApplicationContext());
                } else {
                    if(user.isEmpty()) etUsername.setError("Introduza o username");
                    if(pass.isEmpty()) etPassword.setError("Introduza a password");
                }
            }
        });
    }

    @Override
    public void onValidateLogin(String token, String username, Context context) {
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);

        finish();
    }

    @Override
    public void onLoginError(String message) {
        Toast.makeText(this, message, Toast.LENGTH_LONG).show();
    }
}