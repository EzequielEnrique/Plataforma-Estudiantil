import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from  
 'rxjs';
import { LoginService } from './login.service';

@Injectable({
  providedIn: 'root',
})
export class AuthService implements CanActivate {

  constructor(private loginService: LoginService, private router: Router) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    const isAuthenticated = this.loginService.isAuthenticate();
    const rol = localStorage.getItem('Rol'); 
    const requiredRole = route.data['role']; 
  
    if (isAuthenticated) {
      if (!requiredRole || rol === requiredRole) {
        return true;
      }
  
      if (Array.isArray(requiredRole) && requiredRole.includes(rol)) {
        return true;
      }
  
      this.router.navigate(['/']);
      return false;
    }
  
    this.router.navigate(['/login']);
    return false;
  }

  isAuthenticate(): boolean {
    const token = localStorage.getItem('token');
    return !!token;
  }
}