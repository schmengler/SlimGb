<?php
class SlimGb_Pagination
{
	/**
	 * @var SlimGb_Page[]
	 */
	private $pages = array();
	/**
	 * @var string
	 */
	private $info;

	private $totalEntries;
	private $offset;
	private $totalPages;
	private $currentPage;
	private $entriesPerPage;
	private $maxPageLinks;
	private $pageLinkFormat;
	
	public function __construct(SlimGb_Service_Config  $config, $totalEntries, $offset)
	{
		$this->entriesPerPage = $config['entries']['per_page'];
		$this->maxPageLinks = $config['entries']['max_page_links'];
		$this->pageLinkFormat = $this->getPageLinkFormat($config['application']['link']);
		
		$this->totalEntries = $totalEntries;
		$this->offset = $offset;

		$this->totalPages = ceil(bcdiv($this->totalEntries,$this->entriesPerPage, 1));
		$this->currentPage = round(bcdiv($this->offset, $this->entriesPerPage, 1)) + 1;
		$this->currentPage = min($this->totalPages, $this->currentPage);
		$this->currentPage = max(1, $this->currentPage);
		
		$this->createPages();
		$this->info = sprintf('Showing %d-%d out of %d entries.', $this->pages[$this->currentPage]->from, $this->pages[$this->currentPage]->to, $totalEntries);
	}
	
	public function getPages()
	{
		return $this->pages;
	}
	public function getInfo()
	{
		return $this->info;
	}
	public function getCurrentPage()
	{
		return $this->pages[$this->currentPage];
	}
	
	private function getPageLinkFormat($baseLink)
	{
		$result = $baseLink;
		if (strpos($baseLink, '?')!==false) {
			$result .= '&';
		} else {
			$result .= '?';
		}
		$result .= 'SlimGb_offset=%d';
		return $result;
	}

	private function createPages()
	{
		if ($this->totalPages <= $this->maxPageLinks) {
			$this->createAllPages();
			return;
		}
		$this->createFirstPages();
		$this->createLastPages();
		$this->createCurrentPages();
		ksort($this->pages);
	}
	
	private function createAllPages()
	{
		for($i = 1; $i <= $this->totalPages; ++$i)
		{
			$this->pages[$i] = $this->makePage($this->currentPage == $i ? SlimGb_Page::ACTIVE : SlimGb_Page::LINK, $i);
		}
	}
	
	private function createFirstPages()
	{
		$end = bcdiv($this->maxPageLinks, 3, 0);
		for($i = 1; $i < $end; ++$i) {
			$this->pages[$i] = $this->makePage(SlimGb_Page::LINK, $i);
		}
		$this->pages[$end] = new SlimGb_Page(SlimGb_Page::DOTS);
	}
	
	private function createLastPages()
	{
		$start = $this->totalPages - bcdiv($this->maxPageLinks, 3, 0) + 1;
		for($i = $this->totalPages; $i > $start; --$i) {
			$this->pages[$i] = $this->makePage(SlimGb_Page::LINK, $i);
		}
		$this->pages[$start] = new SlimGb_Page(SlimGb_Page::DOTS);
	}
	
	private function createCurrentPages()
	{
		$this->pages[$this->currentPage] = $this->makePage(SlimGb_Page::ACTIVE, $this->currentPage);
		$i = $this->currentPage;
		$k = 1;
		while(count($this->pages) < $this->maxPageLinks) {
			$i += pow(-1, $k) * $k;
			++$k;
			if ($i >= 1 && $i <= $this->totalPages) {
				$this->pages[$i] = $this->makePage(SlimGb_Page::LINK, $i);
			}
		}
	}
	
	private function makePage($type, $pageNumber)
	{
		return new SlimGb_Page(
			$type,
			$pageNumber,
			1 + $this->entriesPerPage * ($pageNumber - 1),
			min($this->totalEntries, $this->entriesPerPage * ($pageNumber)),
			sprintf($this->pageLinkFormat, $this->entriesPerPage * ($pageNumber -1))
		);
	}
}