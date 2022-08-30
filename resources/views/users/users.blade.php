@if (count($users) > 0)
   <ul class="list-unstyled">
      @foreach ($users as $user)
         <li class="media">
             {{-- ユーザのメールアドレスをもとにGravatarを取得して表示 --}}
             <img class="mr-2 rounded" src="{{ Gravatar::get($user->email, ['size' => 50]) }}" alt="">
             <div class="media-body">
                 <div>
                     {{ $user->name }}
                 </div>
                 <div>
                     {{-- ユーザ詳細ページへのリンク --}}
                     <P>{!! link_to_route('users.show', 'View profile', ['user' => $user->id]) !!}</P>
                 </div>
             </div>
         </li>
        @endforeach 
   </ul>
   {{-- $users->links() --}}

@endif