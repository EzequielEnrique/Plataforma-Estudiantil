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
    const token = localStorage.getItem('Token'); // Obtener el Token almacenado
    if (!token) {
      console.error('Token no encontrado');
      return new Observable(); // Devolver un Observable vacío si no hay Token
    }
  
    const body = { ...libro, Token: token }; // Agregar el Token al cuerpo de la petición
    return this.http.post<any>(`${this.apiUrl}/crearLibro.php`, body);
  }
  

  updateLibro(libro: any, token: string): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/actualizarLibro.php?Token=${token}`, libro);
  }

  deleteLibro(id: number, token: string): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/eliminarLibro.php?id=${id}&Token=${token}`);
  }
  
  
}

