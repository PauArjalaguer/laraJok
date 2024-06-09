<? namespace App\View\Components;

use Illuminate\View\Component;

class MatchesComponent extends Component
{
    public $idGroup;

    public function __construct($idGroup)
    {
        $this->idGroup = $idGroup;
    }

    public function render()
    {
        return view('components.match-component');
    }
} 
?>
 