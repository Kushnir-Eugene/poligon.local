<?php

namespace App\Http\Controllers\Blog\Admin;

use App\Http\Requests\BlogCategoryCreateRequest;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Models\BlogCategory;
use App\Repositories\BlogCategoryRepository;

/**
 * Упраление категориями блога
 * Class CategoryController
 * @package App\Http\Controllers\Blog\Admin
 */
class CategoryController extends BaseController
{
	/**
	 * @var BlogCategoryRepository
	 */
	private $blogCategoryRepository;

	public function __construct()
	{
		parent::__construct();

		$this->blogCategoryRepository = app(BlogCategoryRepository::class);
	}

	/**
     * Управление категориями блога
     *
     * @return App\Http\Controllers\Blog\Admin
     */
    public function index()
    {

        //$paginator = BlogCategory::paginate(15);
		$paginator = $this->blogCategoryRepository->getAllWithPaginate(5);

        return view('blog.admin.categories.index', compact('paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$item = new BlogCategory();
		$categoryList
			= $this->blogCategoryRepository->getForComboBox();

		return view( 'blog.admin.categories.edit',
			compact('item', 'categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogCategoryCreateRequest $request)
    {
		$data = $request->input();
		// ушло в обсервер
//		if (empty($data['slug'])) {
//			$data['slug'] = str_slug($data['title']);
//		}

		//Создаст объект и добивит в БД
		$item = (new BlogCategory())->create($data);

		if ($item){
			return redirect()->route('blog.admin.categories.edit', [$item->id])
				->with(['success' => 'Успешно сохранено']);
		} else {
			return back()->withErrors(['msg' => 'Ошибка сохранения'])
				->withInput();
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {

		$item = $this->blogCategoryRepository->getEdit($id);
		if (empty($item)) {
			abort(404);
		}
		$categoryList
			= $this->blogCategoryRepository->getForComboBox();

		return view('blog.admin.categories.edit',
			compact('item','categoryList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BlogCategoryUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {

		$item = $this->blogCategoryRepository->getEdit($id);

		if (empty($item)){
			return back()
				->withErrors(['msg' => "Запись id=[{$id}] не найдена"])
				->withInput();
		}

		$data = $request->all();

		$result = $item->update($data);

		if($result) {
			return redirect()
				->route('blog.admin.categories.edit', $item->id)
				->with(['success' => 'Успешно сохранено']);
		} else {
			return back()
				->withErrors(['msg' => 'Ошибка сохранения'])
				->withInput();
		}
    }
}
