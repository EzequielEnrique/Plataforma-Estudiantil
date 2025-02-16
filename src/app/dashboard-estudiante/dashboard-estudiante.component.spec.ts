import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DashboardEstudianteComponent } from './dashboard-estudiante.component';

describe('DashboardEstudianteComponent', () => {
  let component: DashboardEstudianteComponent;
  let fixture: ComponentFixture<DashboardEstudianteComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DashboardEstudianteComponent]
    });
    fixture = TestBed.createComponent(DashboardEstudianteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
