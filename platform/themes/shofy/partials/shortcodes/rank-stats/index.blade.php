@php
	use Botble\Media\Facades\RvMedia;

	$title = trim((string) ($shortcode->title ?? ''));

	$rawShowTotal = $shortcode->show_total ?? null;
	$showTotal = $rawShowTotal === null || trim((string) $rawShowTotal) === ''
		? true
		: !in_array(strtolower(trim((string) $rawShowTotal)), ['0', 'false', 'off', 'no'], true);

	$rawIncludeZero = $shortcode->include_zero ?? null;
	$includeZero = $rawIncludeZero === null || trim((string) $rawIncludeZero) === ''
		? true
		: !in_array(strtolower(trim((string) $rawIncludeZero)), ['0', 'false', 'off', 'no'], true);

	// CLONE SLIDES: nếu rank ít (3-5) thì desktop dễ dừng, nên nhân lên tối thiểu 12 slide
	$renderRanks = collect($ranks ?? [])->values();
	$minSlides = 12;

	if ($renderRanks->count() > 0 && $renderRanks->count() < $minSlides) {
		$repeat = (int) ceil($minSlides / $renderRanks->count());
		$tmp = collect();
		for ($i = 0; $i < $repeat; $i++) {
			$tmp = $tmp->merge($renderRanks);
		}
		$renderRanks = $tmp;
	}
@endphp

<div class="affiliate-header mb-4 px-2">
	<div class="row text-center gap-3 mt-3">
		<div>
			@if($title !== '')
				<h3 class="aff-title mb-1">{{ $title }}</h3>
			@endif
			<p class="text-muted small mb-0">{{ __('rank_stats.subtitle') }}</p>
		</div>

		{{-- Nếu cần show total thì mở lại --}}

		@if($showTotal)
			<div class="aff-stats-pill">
				<div class="stats-icon"><i class="fa fa-chart-line"></i></div>
				<div class="stats-text">
					<span class="label">{{ __('rank_stats.community_label') }}:</span>
					<span class="value">{{ number_format((int) ($totalRankedUsers ?? 0)) }}</span>
					<span class="unit">{{ __('rank_stats.members') }}</span>

				</div>
			</div>
		@endif

	</div>

	<div class="swiper rankSwiper">
		<div class="swiper-wrapper">
			@foreach($renderRanks as $rank)
				@php
					$iconPath = trim((string) ($rank->rank_icon ?? ''));
					$iconUrl = $iconPath !== ''
						? (preg_match('~^https?://~i', $iconPath) ? $iconPath : url(ltrim($iconPath, '/')))
						: null;
				@endphp

				<div class="swiper-slide">
					<div class="rank-premium-card">
						<div class="rank-icon-container">
							@if($iconUrl)
								<img src="{{ $iconUrl }}" alt="{{ e($rank->rank_name) }}" class="rank-main-img">
							@else
								<div class="rank-placeholder"><i class="fa fa-medal"></i></div>
							@endif
						</div>

						<div class="rank-details text-center mt-2">
							<div class="rank-name-v2">{{ $rank->rank_name }}</div>
							@php $count = (int) ($rank->users_count ?? 0); @endphp
							<div class="rank-count-v2">
								<span class="num">{{ number_format($count) }}</span>
								<span class="unit">{{ $count === 1 ? __('rank_stats.customer') : __('rank_stats.customers') }}</span>
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>

<style>
	.aff-title {
		font-size: 1.6rem;
		font-weight: 800;
		color: #0f172a;
		letter-spacing: -0.02em;
		background: linear-gradient(90deg, #1e293b, #6366f1);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
	}

	@media (max-width: 576px) {
		.aff-title {
			font-size: 1.3rem;
		}
	}

	.rank-premium-card {
		background: linear-gradient(145deg, #ffffff, #f8fafc);
		border: 1px solid rgba(226, 232, 240, 0.8);
		border-radius: 10px;
		padding: 10px 8px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		transition: all 0.3s ease;
		min-height: 120px;
		cursor: pointer;
		user-select: none;
		will-change: transform, box-shadow, border-color;
		position: relative;
		overflow: hidden;
		transition:
			transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
			box-shadow 0.4s ease,
			border-color 0.4s ease;
	}

	/* Hiệu ứng tia sáng quét ngang (Shine effect) khi hover */
	.rank-premium-card::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(to right,
				transparent,
				rgba(255, 255, 255, 0.43),
				transparent);
		transform: skewX(-25deg);
		transition: 2s;
		pointer-events: none;
	}

	.rank-premium-card:hover {
		border-color: #6366f1;
		transform: translateY(-10px) scale(1.03);
		box-shadow: 0 20px 40px rgba(99, 102, 241, 0.25),
			0 0 15px rgba(99, 102, 241, 0.1);
	}

	.rank-premium-card:hover::before {
		left: 150%;
		/* Tia sáng chạy từ trái sang phải */
	}

	/* Click (active) */
	.rank-premium-card:active {
		/* transform: translateY(-4px) scale(0.96); */
		box-shadow: 0 8px 15px rgba(99, 102, 241, 0.2);
		transition: transform 0.1s ease;
	}

	/* Khi click active */
.rank-premium-card:active .rank-icon-container {
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
}
	/* Khi hover vào cả cái card lớn */
.rank-premium-card:hover .rank-icon-container {
  /* Icon phóng to hơn chút nữa khi hover */
  transform: scale(1.1);
  /* Tắt animation thở khi đang hover để tránh xung đột */
  animation: none;
  /* Hào quang sáng mạnh khi hover */
  box-shadow: 0 0 35px rgba(99, 102, 241, 0.6);
}
	/* Đổi màu số lượng khi hover để tạo điểm nhấn */
	.rank-premium-card:hover .rank-count-v2 .num {
		color: #4f46e5;
		text-shadow: 0 0 10px rgba(99, 102, 241, 0.3);
	}

/* Container chính - đóng vai trò là viền ngoài phát sáng */
.rank-icon-container {
  position: relative;
  width: 84px; /* Tăng kích thước lên một chút */
  height: 84px;
  /* Tạo viền gradient màu tím sang trọng */
  background: linear-gradient(135deg, #c4b5fd 0%, #6366f1 100%);
  border-radius: 50%; /* Chuyển thành hình tròn */
  padding: 4px; /* Độ dày của viền gradient */
  
  display: flex;
  align-items: center;
  justify-content: center;
  
  /* Hiệu ứng phát sáng hào quang bên ngoài */
  box-shadow: 0 0 20px rgba(99, 102, 241, 0.3),
              0 4px 6px rgba(0, 0, 0, 0.1);
              
  transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  
  /* Animation nhịp thở nhẹ cho viền */
  animation: rankPulse 3s infinite ease-in-out;
}
	/* Lớp nền bên trong để tạo độ sâu (Inset) */
.rank-icon-container::before {
  content: '';
  position: absolute;
  inset: 4px; /* Cách viền ngoài 4px */
  background: #ffffff; /* Nền trắng bên trong */
  border-radius: 50%;
  /* Đổ bóng vào trong tạo cảm giác lõm xuống */
  box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.06),
              inset 0 -2px 4px rgba(255, 255, 255, 0.8);
  z-index: 1;
}
/* Ảnh icon chính */
.rank-main-img {
  position: relative; /* Để nổi lên trên lớp nền ::before */
  z-index: 2;
  width: 70%; /* Kích thước ảnh so với vòng tròn */
  height: 70%;
  object-fit: contain;
  /* Đổ bóng cho chính icon để nó "trôi" lên khỏi nền */
  filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.15));
  transition: transform 0.3s ease;
}
/* Placeholder khi không có ảnh */
.rank-placeholder {
  position: relative;
  z-index: 2;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  /* Màu gradient cho icon placeholder */
  background: linear-gradient(135deg, #818cf8, #4f46e5);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  filter: drop-shadow(0 2px 4px rgba(99, 102, 241, 0.3));
}
	.rank-name-v2 {
		font-weight: 800;
		font-size: 0.95rem;
		color: #1e293b;
		margin-bottom: 4px;
		white-space: nowrap;
	}

	.rank-count-v2 .num {
		font-weight: 700;
		font-size: 0.85rem;
		color: #6366f1;
	}

	.rank-count-v2 .unit {
		font-size: 0.7rem;
		color: #94a3b8;
		text-transform: uppercase;
	}

	.rankSwiper {
		padding: 10px !important;
		overflow: hidden;
	}

	/* Marquee mượt */
	.rankSwiper .swiper-wrapper {
		transition-timing-function: linear !important;
	}

	/* Fix: slide width cố định để "hộp" không phình */
	.rankSwiper .swiper-slide {
		width: 240px !important;
	}

	@media (max-width: 576px) {
		.rankSwiper .swiper-slide {
			width: 160px !important;
		}
	}

	@media (min-width: 1400px) {
		.rankSwiper .swiper-slide {
			width: 220px !important;
		}
	}

	/* Mặc định hơi "nhạt" để khi vào giữa mới nổi */
	.rankSwiper .swiper-slide {
		opacity: .55;
		transform: scale(.92);
		/* Thu nhỏ hơn chút nữa để khi nổi bật sẽ rõ hơn */
		transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1),
			opacity 0.4s ease,
			filter 0.4s ease;
		will-change: transform, opacity;
		/* Tối ưu hiệu năng render */
	}

	.rankSwiper .swiper-slide.is-center {
		opacity: 1;
		transform: scale(1.08);
		/* To hơn chút nữa */
		filter: drop-shadow(0 15px 30px rgba(99, 102, 241, .3));
		z-index: 10;
		/* Đè lên các thằng bên cạnh */
	}

	/* Nếu muốn hộp cũng glow thêm */
	.rankSwiper .swiper-slide.is-center .rank-premium-card {
		border-color: #6366f1;
		box-shadow: 0 18px 40px rgba(99, 102, 241, .18);
	}
	/* Keyframes cho hiệu ứng nhịp thở */
@keyframes rankPulse {
  0% {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.3), 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  50% {
    /* Khi thở ra thì hào quang mạnh hơn chút */
    box-shadow: 0 0 30px rgba(99, 102, 241, 0.5), 0 8px 12px rgba(0, 0, 0, 0.1);
  }
  100% {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.3), 0 4px 6px rgba(0, 0, 0, 0.1);
  }
}
</style>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const el = document.querySelector('.rankSwiper');
		if (!el) return;

		const slidesCount = el.querySelectorAll('.swiper-slide').length;

		const swiper = new Swiper('.rankSwiper', {
			slidesPerView: 'auto',
			spaceBetween: 20,
			centeredSlides: false, // Tắt cái này để ta tự handle class

			loop: slidesCount > 1,
			// Tăng số lượng slide ảo để loop mượt hơn trên màn hình to
			loopedSlides: 10,

			// Tốc độ chạy marquee
			speed: 4500,
			autoplay: {
				delay: 0, // Delay 0 để chạy liên tục như băng chuyền
				disableOnInteraction: false,
				pauseOnMouseEnter: false,
			},

			// Hiệu ứng chuyển động mượt (linear)
			freeMode: {
				enabled: true,
				momentum: false,
			},

			grabCursor: true,
			allowTouchMove: true,

			// Quan trọng: Update lại highlight khi loop
			on: {
				init() {
					this.update();
					highlightVisualCenter(this);
				},
				// Hook vào sự kiện transition của marquee
				sliderMove() {
					highlightVisualCenter(this);
				},
				setTranslate() {
					highlightVisualCenter(this);
				},
				resize() {
					highlightVisualCenter(this);
				}
			}
		});

		/**
		 * Hàm tính toán dựa trên vị trí hiển thị thực tế (Visual Position)
		 * Thay vì dùng toạ độ ảo của Swiper
		 */
		function highlightVisualCenter(sw) {
			// 1. Lấy khung bao hiển thị (Container)
			const containerRect = sw.el.getBoundingClientRect();
			const containerCenter = containerRect.left + (containerRect.width / 2);

			// Biến để tìm slide gần tâm nhất
			let closestSlide = null;
			let minDistance = Infinity;

			// 2. Duyệt qua các slide ĐANG hiển thị trên màn hình
			// sw.slides bao gồm cả các slide copy do loop tạo ra
			for (let i = 0; i < sw.slides.length; i++) {
				const slide = sw.slides[i];

				// Lấy vị trí thực tế của slide
				const slideRect = slide.getBoundingClientRect();

				// Chỉ tính toán những slide đang nằm trong hoặc gần khung nhìn (Optimization)
				// Mở rộng biên ra 1 chút để bắt dính mượt hơn
				if (slideRect.right < 0 || slideRect.left > window.innerWidth) {
					slide.classList.remove('is-center');
					continue;
				}

				const slideCenter = slideRect.left + (slideRect.width / 2);

				// Tính khoảng cách từ tâm slide đến tâm container
				const dist = Math.abs(slideCenter - containerCenter);

				if (dist < minDistance) {
					minDistance = dist;
					closestSlide = slide;
				}

				// Reset class trước
				slide.classList.remove('is-center');
			}

			// 3. Active slide gần nhất
			// Thêm ngưỡng (threshold) nhỏ nếu muốn: vd dist < 50px mới active
			if (closestSlide) {
				closestSlide.classList.add('is-center');
			}
		}
	});
</script>