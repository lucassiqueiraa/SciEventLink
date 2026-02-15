package com.scieventlink.app.adapters;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.viewpager2.adapter.FragmentStateAdapter;
import com.scieventlink.app.fragments.SessionInfoFragment;
import com.scieventlink.app.fragments.SessionQAFragment;



public class SessionPagerAdapter extends FragmentStateAdapter {
    private int sessionId;
    private String title;
    private String description;

    public SessionPagerAdapter(@NonNull FragmentActivity fragmentActivity, int id, String title, String desc) {
        super(fragmentActivity);
        this.sessionId = id;
        this.title = title;
        this.description = desc;
    }

    @Override
    public Fragment createFragment(int position) {
        if (position == 0) {
            return SessionInfoFragment.newInstance(sessionId, title, description);
        } else {
            return SessionQAFragment.newInstance(sessionId);
        }
    }

    @Override
    public int getItemCount() {
        return 2;
    }
}