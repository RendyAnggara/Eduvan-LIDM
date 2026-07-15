import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth';
import { SearchService } from '../services/search';
import { CourseService } from '../services/course.service';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  namaUser: string = 'User';
  keywordPencarian: string = '';
  isLoading: boolean = true;
  kursusTersaring: any[] = [];
  unreadCount: number = 0;
  selectedCategory: string | null = null;
  courses: any[] = [];
  coursesWajib: any[] = [];
  coursesPilihan: any[] = [];

  constructor(
    private router: Router,
    private authService: AuthService,
    private searchService: SearchService,
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.ambilNamaUserLive();
    this.muatDataBerandaTotal();
    this.loadCourses();

    this.courseService.notifChanged$.subscribe((berubah: boolean) => {
      if (berubah) {
        this.muatJumlahNotifikasi();
      }
    });
  }

  selectCategory(kategori: string) {
    if (this.selectedCategory === kategori) {
      this.selectedCategory = null;
      this.kursusTersaring = this.courses;
    } else {
      this.selectedCategory = kategori;
      // Memfilter array berdasarkan course_type dari backend
      this.kursusTersaring = this.courses.filter(
        (k: any) => k.course_type === kategori
      );
    }
    this.cdr.detectChanges();
  }

  loadCourses() {
    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        this.courses = res.data;
        this.kursusTersaring = res.data;
      },
    });
  }

  ionViewWillEnter() {
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (localUserData) {
      try {
        const user = JSON.parse(localUserData);
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap.split(' ')[0];
        this.cdr.detectChanges();
      } catch (e) {
        console.error('Gagal parse user data di beranda:', e);
      }
    }

    this.muatJumlahNotifikasi();
    this.muatDataBerandaTotal();
  }

  muatDataBerandaTotal(refresherEvent?: CustomEvent) {
    if (!refresherEvent) {
      this.isLoading = true;
    }

    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        // Simpan respons data polosan tanpa manipulasi map string aneh-aneh
        const dataAsli = res.data || [];
        this.courses = dataAsli;

        // Kembalikan filter sesuai status tombol yang sedang aktif
        if (this.selectedCategory) {
          this.kursusTersaring = dataAsli.filter(
            (k: any) => k.course_type === this.selectedCategory
          );
        } else {
          this.kursusTersaring = dataAsli;
        }
      },
      error: (err) => {
        console.error('Gagal memuat dari Ngrok:', err);
      },
      complete: () => {
        this.isLoading = false;
        if (refresherEvent) {
          (refresherEvent.target as any).complete();
        }
        this.cdr.detectChanges();
      },
    });
  }

  handleRefresh(event: CustomEvent) {
    console.log('User melakukan refresh halaman...');

    this.ngOnInit();

    setTimeout(() => {
      if (event && event.target) {
        (event.target as any).complete();
      }
    }, 800);
  }

  muatJumlahNotifikasi() {
    this.courseService.getNotificationsCount().subscribe({
      next: (res: any) => {
        if (res && res.status === 'success') {
          this.unreadCount = res.unread_count;
          this.cdr.detectChanges();
        }
      },
      error: (err: any) => {
        console.error('Gagal memuat jumlah notifikasi:', err);
      },
    });
  }

  ambilNamaUserLive() {
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap.split(' ')[0];
        this.cdr.detectChanges();
      } else {
        this.namaUser = 'User';
      }
    });
  }

  goToDetail(id?: any) {
    if (id) {
      this.router.navigate(['/course-detail', id]);
    } else {
      this.router.navigate(['/course-detail']);
    }
  }

  goToBannerDetail() {
    this.router.navigate(['/tabs/course']);
  }

  fungsiCariKursus() {
    const keyword = this.keywordPencarian.trim();
    this.searchService.changeKeyword(keyword);
    this.router.navigate(['/tabs/course']);
    this.keywordPencarian = '';
  }

  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  goToCourse() {
    this.router.navigateByUrl('/tabs/course');
  }

  getDefaultImage(category: string): string {
    if (!category) return 'assets/icon/computer-science.jpeg';
    const kat = category.toLowerCase();
    if (
      kat.includes('computer') ||
      kat.includes('science') ||
      kat.includes('coding')
    ) {
      return 'assets/icon/computer-science.jpeg';
    } else if (
      kat.includes('microsoft') ||
      kat.includes('office') ||
      kat.includes('excel')
    ) {
      return 'assets/icon/microsoft-office.jpeg';
    }
    return 'assets/icon/computer-science.jpeg';
  }

  handleImageError(event: any, category: string) {
    event.target.src = this.getDefaultImage(category);
  }

  bukaChatCS() {
    const pesan = 'Halo Admin EduVan, saya ingin bertanya mengenai kursus...';
    const nomorWA = '628978665982';
    window.open(
      `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`,
      '_blank'
    );
  }
}
