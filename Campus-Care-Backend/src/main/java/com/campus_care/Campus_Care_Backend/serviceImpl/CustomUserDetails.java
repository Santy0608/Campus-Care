package com.campus_care.Campus_Care_Backend.serviceImpl;

import org.springframework.security.core.GrantedAuthority;

import java.util.Collection;

public class CustomUserDetails extends org.springframework.security.core.userdetails.User {


    private final String id;

    public CustomUserDetails(String id, String username, String password,
                             Collection<? extends GrantedAuthority> authorities) {
        super(username, password, true, true, true, true, authorities);
        this.id = id;
    }

    public String getId() {
        return id;
    }
}
