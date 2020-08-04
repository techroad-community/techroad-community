<link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">


<form id="brandh-form-form" class="" action="#" method="post" data-url="<?php echo admin_url('admin-ajax.php'); ?>" >
	
	<div class="border p-4 bg-gray-200 text-2xl flex">
		<div class="flex items-center"><i class="fa fa-arrow-right" aria-hidden="true" style="font-size:12px"></i></div>
		<div class="ml-2">간편발주</div>
	</div>

	<div class="bg-white lg:flex mt-4">
		<div class="lg:p-4 border lg:m-4 p-4 h-full lg:w-4/12 w-full">
			<div class=flex>
				<div class="font-bold text-black">광고</div><div class="text-gray"><span style="padding:0 5px">·</span>www.techroad.io</div>
			</div>
			<a href="www.naver.com" class="text-lg break-all" style="word-break:keep-all;">테크로드 외주토론방</a>
			<div style="word-break:keep-all;">카카오개발자, 네이버개발자등에게 100만원이하의 간단한 외주를 주거나 토론할수있는 방에 입장하여 도움을 요청해보세요</div>
		</div>
		<div class="flex flex-col w-full max-w-sm mx-auto p-4 ">
			<div class="flex flex-col mb-4 w-full">
				<label for="name"
					class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">
					성함을 알려주세요
				</label>

				<div class="relative">

					<div class="absolute flex border border-transparent left-0 top-0 h-full w-10">
						<div class="flex items-center justify-center rounded-tl rounded-bl z-10 bg-gray-100 text-gray-600 text-lg h-full w-full">
						<i class="fas fa-male"></i>
						</div>
					</div>

					<input id="name"
						name="name"
						type="text"
						placeholder="성함"
						value=""
						class="text-sm sm:text-base relative w-full border rounded placeholder-gray-400 focus:border-indigo-400 focus:outline-none py-2 pr-2 pl-12" style="padding-left:3rem;padding-top:0.5rem;padding-bottom:0.5rem;margin-bottom:0px"
						required>
						<small class="field-msg error" data-error="invalidName">This feild is Required</small>
				</div>
			</div>
			<!-- Start-->
			<div class="flex flex-col mb-4 ">
				<label for="email"
					class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">
					이메일을 알려주세요
				</label>

				<div class="relative">

					<div class="absolute flex border border-transparent left-0 top-0 h-full w-10">
						<div class="flex items-center justify-center rounded-tl rounded-bl z-10 bg-gray-100 text-gray-600 text-lg h-full w-full">
						<i class="fas fa-envelope-square"></i>
						</div>
					</div>

					<input id="email"
						name="email"
						type="email"
						placeholder="이메일"
						value=""
						class="text-sm sm:text-base relative w-full border rounded placeholder-gray-400 focus:border-indigo-400 focus:outline-none py-2 pr-2 pl-12 border-red-500"
						style="padding-left:3rem;padding-top:0.5rem;padding-bottom:0.5rem;margin-bottom:0px"
						required>
						<small class="field-msg error" data-error="invalidEmail">This feild is not valid</small>
				</div>
			</div>
			<!-- End -->
			<!-- Start-->
			<div class="flex flex-col mb-4 ">
				<label for="phone"
					class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">
					연락처를 알려주세요
				</label>

				<div class="relative">

					<div class="absolute flex border border-transparent left-0 top-0 h-full w-10">
						<div class="flex items-center justify-center rounded-tl rounded-bl z-10 bg-gray-100 text-gray-600 text-lg h-full w-full">
						<i class="fas fa-phone"></i>
						</div>
					</div>

					<input id="phone"
						name="phone"
						type="text"
						placeholder="연락처"
						value=""
						class="text-sm sm:text-base relative w-full border rounded placeholder-gray-400 focus:border-indigo-400 focus:outline-none py-2 pr-2 pl-12 border-red-500"
						style="padding-left:3rem;padding-top:0.5rem;padding-bottom:0.5rem;margin-bottom:0px"
						required>
						<small class="field-msg error" data-error="invalidEmail">This feild is not valid</small>
				</div>
			</div>
			<!-- End -->
		
			<!-- Start-->
			<div class="flex flex-col mb-4 ">
				<label for="care"
					class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">
					소속/직함을 알려주세요
				</label>

				<div class="relative">

					<div class="absolute flex border border-transparent left-0 top-0 h-full w-10">
						<div class="flex items-center justify-center rounded-tl rounded-bl z-10 bg-gray-100 text-gray-600 text-lg h-full w-full">
						<i class="fas fa-building"></i>
						</div>
					</div>

					<input id="care"
						name="care"
						type="text"
						placeholder="소속/직함"
						value=""
						class="text-sm sm:text-base relative w-full border rounded placeholder-gray-400 focus:border-indigo-400 focus:outline-none py-2 pr-2 pl-12 border-red-500"
						style="padding-left:3rem;padding-top:0.5rem;padding-bottom:0.5rem;margin-bottom:0px"
						required>
						<small class="field-msg error" data-error="invalidCare">This feild is not valid</small>
				</div>
			</div>
			<!-- End -->
			<!-- Start-->
			<div class="flex flex-col mb-4 ">
				<label for="message"
					class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">
					문의내용을 알려주세요
				</label>

				<div class="relative">

					<div class="absolute flex border border-transparent left-0 top-0 h-full w-10">
						<div class="flex items-center justify-center rounded-tl rounded-bl z-10 bg-gray-100 text-gray-600 text-lg h-full w-full">
						<i class="fas fa-book-open"></i>
						</div>
					</div>

					<textarea id="message"
						name="message"
						
						placeholder="문의 내용"
						
						class="field-input w-full h-full"
						style="padding-left:3rem;padding-top:0.5rem;padding-bottom:0.5rem;margin-bottom:0px"
						required></textarea>
					<small class="field-msg error" data-error="invalidMessage">A feild is Required</small>
				</div>
			</div>
			<!-- End -->
		</div>
		
	</div>
	<div class="field-container flex justify-center">
		<div>
			<button type="submit" class="btn btn-default btn-lg btn-sunset-form  border py-2 px-5 rounded-full">제출하기</button>
		</div>
		<div class="field-msg js-form-submission">제출이 진행중입니다. 잠시만 기다려주세요.&hellip;</div>
		<div class="field-msg success js-form-success">제출에 성공했습니다.</div>
		<div class="field-msg error js-form-error">서버에 문제가 있었어요. 다시 시도해보세요!</div>
	</div>

	<input type="hidden" name="action" value="submit_testimonial">
	<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("testimonial-nonce") ?>">


	
</form>