import { HttpClient, HttpErrorResponse, HttpResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs';
import { jwtDecode } from 'jwt-decode';



@Injectable({
  providedIn: 'root'
})
export class LoginService {

  private apiUrl = 'http://localhost/Final/API';
  private statusCode: any;

  constructor(private http: HttpClient) { }

  login(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login.php`, data);
  }

  register(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/register.php`, data).pipe(
      catchError(error => {
        return throwError(() => new Error(error.error.message || 'Ocurrió un error en el servidor.'));
      })
    );
  }
  //Método que devuelve si el login es válido
  isAuthenticate(): boolean {
    const token: any = localStorage.getItem('Token');
    
    if (token == null){
      return false;
    }else{
      const decoded: any = jwtDecode(token);
      const fechaActual = Date.now() / 1000; //Convertir a segundos
      //Devuelve falso si el token es válido
      if (decoded.exp < fechaActual){
        return false;
      }else {
        return true;
      }
    }


    const decoded: any = jwtDecode(token);
    const fechaActual = Date.now() / 1000; //Convertir a segundos
    //Devuelve falso si el token es válido
    return decoded.exp < fechaActual;
    
    
  }
}
