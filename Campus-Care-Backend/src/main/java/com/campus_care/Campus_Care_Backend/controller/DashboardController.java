package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.DashboardDTO;
import com.campus_care.Campus_Care_Backend.service.DashboardService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/dashboard")
public class DashboardController {

    @Autowired
    private DashboardService dashboardService;

    @GetMapping("/estudiante/{idUsuario}")
    public ResponseEntity<DashboardDTO> obtenerDashboard(@PathVariable String idUsuario) {
        return ResponseEntity.ok(dashboardService.obtenerDashboardEstudiante(idUsuario));
    }


}
