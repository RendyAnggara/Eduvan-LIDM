import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { AuthService } from '../services/auth';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(private auth: AuthService, private router: Router) {}

// src/app/guards/auth-guard.ts
canActivate(): boolean {
  // Cek langsung ke sumber kebenaran (localStorage)
  const token = localStorage.getItem('token');
  
  if (token && token !== '') {
    return true; 
  } else {
    // Jika tidak ada token, paksa kembali ke login
    this.router.navigateByUrl('/login');
    return false;
  }
}
}