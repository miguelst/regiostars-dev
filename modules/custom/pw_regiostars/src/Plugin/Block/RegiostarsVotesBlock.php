<?php

namespace Drupal\pw_regiostars\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Votes' Block.
 *
 * @Block(
 *   id = "reqiostars_votes_block",
 *   admin_label = @Translation("Regiostars Votes block"),
 *   category = @Translation("Regiostars Votes block"),
 * )
 */
class RegiostarsVotesBlock extends BlockBase {

	/**
	 * {@inheritdoc}
	 */
	public function build() {
		$db = \Drupal::database();
		
		$res = $db->query("SELECT field_rg_prj_real_title_value as `project_title`, nb_votes  FROM (SELECT count(sid) AS nb_votes,value FROM webform_submission_data where name = 'projectid' group by value) AS votes INNER JOIN node__field_rg_prj_real_title ON votes.value = node__field_rg_prj_real_title.entity_id;");
		$votes = $res->fetchAll();
		
		
		$form['regiostars_votes'] = array(
				'#type' => 'table',
				'#header' => array($this->t('Project title'), $this->t('Number of votes')),
		);
		
		foreach ($votes as $tmpvote) {			
			$formvote['#attributes'] = array('class' => array('foo', 'baz'));
			$formvote['title'] = array(
				'#markup' => $tmpvote->project_title
			);
			$formvote['votes'] = array(
					'#markup' => $tmpvote->nb_votes
			);				
			$form['regiostars_votes'][] = $formvote;
		}
		
		return array(
		//		'#markup' => print_r($votes, TRUE),
				$form['regiostars_votes']
		);
	}

}
