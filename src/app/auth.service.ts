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
    const rol = localStorage.getItem('Rol'); // Rol actual del usuario
    const requiredRole = route.data['role']; // Rol requerido por la ruta
  
    if (isAuthenticated) {
      // Si no se especifica un rol o el rol del usuario coincide con el requerido
      if (!requiredRole || rol === requiredRole) {
        return true;
      }
  
      // Si se permiten múltiples roles en la ruta (array de roles)
      if (Array.isArray(requiredRole) && requiredRole.includes(rol)) {
        return true;
      }
  
      // Si el rol no coincide, redirige a una página de error o acceso denegado
      this.router.navigate(['/']);
      return false;
    }
  
    // Si no está autenticado, redirige al login
    this.router.navigate(['/login']);
    return false;
  }

  isAuthenticate(): boolean {
    const token = localStorage.getItem('token');
    // Aquí puedes agregar lógica adicional para verificar si el token es válido
    return !!token;
  }
}