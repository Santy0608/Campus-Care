package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.DashboardAdminDTO;
import com.campus_care.Campus_Care_Backend.service.DashboardAdminService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/dashboard-admin")
public class DashboardAdminController {

    @Autowired
    private DashboardAdminService dashboardAdminService;

    @GetMapping
    public DashboardAdminDTO obtenerDashboardAdmin(){
        return dashboardAdminService.obtenerDatosDashboard();
    }


}
