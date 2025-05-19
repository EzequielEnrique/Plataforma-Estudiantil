import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LibrosService {
  private apiUrl = 'http://localhost/Final/API';

  constructor(private http: HttpClient) {}

  getLibros(token: string): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/obtenerLibros.php?Token=${token}`);
  }
  

  createLibro(libro: any): Observable<any> {
    const token = localStorage.getItem('Token'); 
    if (!token) {
      console.error('Token no encontrado');
      return new Observable(); 
    }
  
    const body = { ...libro, Token: token }; 
    return this.http.post<any>(`${this.apiUrl}/crearLibro.php`, body);
  }
  

  updateLibro(libro: any, token: string): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/actualizarLibro.php?Token=${token}`, libro);
  }

  deleteLibro(id: number, token: string): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/eliminarLibro.php?id=${id}&Token=${token}`);
  }
  
  
}

