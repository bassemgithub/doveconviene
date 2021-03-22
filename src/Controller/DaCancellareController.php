<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * DaCancellare Controller
 *
 * @method \App\Model\Entity\DaCancellare[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DaCancellareController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $daCancellare = $this->paginate($this->DaCancellare);

        $this->set(compact('daCancellare'));
    }

    /**
     * View method
     *
     * @param string|null $id Da Cancellare id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $daCancellare = $this->DaCancellare->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('daCancellare'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $daCancellare = $this->DaCancellare->newEmptyEntity();
        if ($this->request->is('post')) {
            $daCancellare = $this->DaCancellare->patchEntity($daCancellare, $this->request->getData());
            if ($this->DaCancellare->save($daCancellare)) {
                $this->Flash->success(__('The da cancellare has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The da cancellare could not be saved. Please, try again.'));
        }
        $this->set(compact('daCancellare'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Da Cancellare id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $daCancellare = $this->DaCancellare->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $daCancellare = $this->DaCancellare->patchEntity($daCancellare, $this->request->getData());
            if ($this->DaCancellare->save($daCancellare)) {
                $this->Flash->success(__('The da cancellare has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The da cancellare could not be saved. Please, try again.'));
        }
        $this->set(compact('daCancellare'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Da Cancellare id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $daCancellare = $this->DaCancellare->get($id);
        if ($this->DaCancellare->delete($daCancellare)) {
            $this->Flash->success(__('The da cancellare has been deleted.'));
        } else {
            $this->Flash->error(__('The da cancellare could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
