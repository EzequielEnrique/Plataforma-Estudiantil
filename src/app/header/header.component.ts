import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { LoginService } from '../login.service';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {
  isMenuOpen = false;

  constructor(private loginService: LoginService, private router: Router) {}

  ngOnInit() {
    
    if (!this.loginService.isAuthenticate()) {
      localStorage.removeItem('Token');
      localStorage.removeItem('Rol');
    }
  }

  toggleMenu() {
    this.isMenuOpen = !this.isMenuOpen;
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('Token');
  }

  getInicioLink(): string {
    const token = localStorage.getItem('Token');
    if (this.loginService.isAuthenticate() && token) {
      const rol = localStorage.getItem('Rol');
      if (rol === 'Bibliotecario') {
        return '/dashboard-bibliotecario';
      } else if (rol === 'Estudiante') {
        return '/dashboard-estudiante';
      }
    }
    return '/';
  }

  logout() {
    localStorage.removeItem('Token');
    localStorage.removeItem('Rol');
    this.router.navigate(['/login']);
  }
}


