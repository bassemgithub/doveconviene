<?php
declare(strict_types=1);

namespace flayers\Controller;

use App\Controller\AppController;

/**
 * Json Controller
 *
 * @method \flayers\Model\Entity\Json[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class JsonController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $json = $this->paginate($this->Json);

        $this->set(compact('json'));
    }

    /**
     * View method
     *
     * @param string|null $id Json id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $json = $this->Json->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('json'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $json = $this->Json->newEmptyEntity();
        if ($this->request->is('post')) {
            $json = $this->Json->patchEntity($json, $this->request->getData());
            if ($this->Json->save($json)) {
                $this->Flash->success(__('The json has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The json could not be saved. Please, try again.'));
        }
        $this->set(compact('json'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Json id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $json = $this->Json->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $json = $this->Json->patchEntity($json, $this->request->getData());
            if ($this->Json->save($json)) {
                $this->Flash->success(__('The json has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The json could not be saved. Please, try again.'));
        }
        $this->set(compact('json'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Json id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $json = $this->Json->get($id);
        if ($this->Json->delete($json)) {
            $this->Flash->success(__('The json has been deleted.'));
        } else {
            $this->Flash->error(__('The json could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
