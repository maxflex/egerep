angular.module 'Egerep'
    .service 'BranchService', (Branches)->
        this.branches = Branches

        this.getNameWithColor = (branch_id) ->
            curBranch = this.branches[branch_id]
            '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" class="svg-metro"><circle fill="' + curBranch.color + '" r="6" cx="7" cy="7"></circle></svg>' + curBranch.full

        this