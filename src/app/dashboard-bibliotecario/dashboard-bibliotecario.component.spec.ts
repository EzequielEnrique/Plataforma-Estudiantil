import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DashboardBibliotecarioComponent } from './dashboard-bibliotecario.component';

describe('DashboardBibliotecarioComponent', () => {
  let component: DashboardBibliotecarioComponent;
  let fixture: ComponentFixture<DashboardBibliotecarioComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [DashboardBibliotecarioComponent]
    });
    fixture = TestBed.createComponent(DashboardBibliotecarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
